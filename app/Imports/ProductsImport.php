<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\ItemUnit;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;

class ProductsImport implements ToCollection, WithStartRow, WithChunkReading, ShouldQueue
{

    protected int $outlet_id;

    protected User $user;

    protected array $error_messages = [];

    public function __construct(int $outlet_id, User $user) {
        $this->outlet_id = $outlet_id;
        $this->user = $user;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {

        try {

            $iteration = 1;

            foreach ($rows as $row) {

                DB::beginTransaction();

                # have stock
                $have_stock = $this->have_stock($row[4]);

                # cek produk
                $product = Product::where('slug', Str::slug($row[1] . ' ' . $this->outlet_id))->first();

                # kalo produknya udah pernah di input, skip aja wkwk
                if ($product) {
                    DB::rollBack();

                    $error_message = '[import-product] product |'. $row[1] .'| sudah ada. row: ' . $iteration + 1;

                    array_push($this->error_messages, $error_message);
                    Log::alert($error_message);

                    $iteration += 1;
                    continue;
                }

                # cek item unit
                $item_unit = ItemUnit::where('slug', Str::slug($row[2]))->first();
                if (!$item_unit) {
                    $item_unit = new ItemUnit();
                    $item_unit->name = $row[2];

                    if (!$item_unit->save()) {
                        DB::rollBack();

                        $error_message = '[import-product] unable to create item unit on product |'. $product->name .'| row: ' . $iteration + 1;

                        array_push($this->error_messages, $error_message);
                        Log::alert($error_message);

                        $iteration += 1;
                        continue;
                    }
                }

                # create product
                $product = new Product();

                $product->name = $row[1];
                $product->outlet_id = $this->outlet_id;
                $product->item_unit_id = $item_unit->id;
                $product->have_stock = $have_stock;
                $product->created_by = $this->user->id;

                if (!$product->save()){
                    DB::rollBack();

                    $error_message = '[import-product] unable to create product |'. $product->name .'| row: ' . $iteration + 1;

                    array_push($this->error_messages, $error_message);
                    Log::alert($error_message);

                    $iteration += 1;
                    continue;
                }

                # create product price
                $product_price = new ProductPrice();

                $product_price->product_id = $product->id;
                $product_price->base_price = (int) $row[5];
                $product_price->margin = (int) $row[7];
                $product_price->created_by = $this->user->id;

                if (!$product_price->save()) {
                    DB::rollBack();

                    $error_message = '[import-product] unable to create product price of product |'. $product->name .'| row: ' . $iteration + 1;

                    array_push($this->error_messages, $error_message);
                    Log::alert($error_message);

                    $iteration += 1;
                    continue;
                }

                # category
                $category = Category::where('slug', Str::slug($row[3]))->first();
                if (!$category) {
                    $category = new Category();
                    $category->name = $row[3];
                    if (!$category->save()) {
                        DB::rollBack();

                        $error_message = '[import-product] unable to create category for product |'. $product->name .'| row: ' . $iteration + 1;

                        array_push($this->error_messages, $error_message);
                        Log::alert($error_message);

                        $iteration += 1;
                        continue;
                    }
                }

                # product category
                $product_category = new ProductCategory();
                $product_category->product_id = $product->id;
                $product_category->category_id = $category->id;
                if (!$product_category->save()) {
                    DB::rollBack();

                    $error_message = '[import-product] unable to create product category of product |'. $product->name .'| row: ' . $iteration + 1;

                    array_push($this->error_messages, $error_message);
                    Log::alert($error_message);

                    $iteration += 1;
                    continue;
                }

                # cek apakah produk ini punya stok atau tidak
                if (!$have_stock) {
                    DB::commit();
                    Log::info('Berhasil create produk '. $product->name . ' row: ' . $iteration + 1);
                    $iteration += 1;

                    continue;
                }

                if ($row[8] == '1') {
                    $row[8] = 'beli-sendiri';
                }

                # cek supplier
                $supplier = Supplier::where('slug', Str::slug($row[8]))->first();
                if (!$supplier) {
                    $supplier = new Supplier();
                    $supplier->name = $row[8];
                    if (!$supplier->save()) {
                        DB::rollBack();

                        $error_message = '[import-product] unable to create supplier for product |'. $product->name .'| row: ' . $iteration + 1;

                        array_push($this->error_messages, $error_message);
                        Log::alert($error_message);

                        $iteration += 1;
                        continue;
                    }
                }

                # create stock
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->supplier_id = $supplier->id;
                $product_stock->stock = (int) $row[7];
                $product_stock->status = 'in';
                $product_stock->created_by = $this->user->id;
                if (!$product_stock->save()) {
                    DB::rollBack();

                    $error_message = '[import-product] unable to create product stock for product |'. $product->name .'| row: ' . $iteration + 1;

                    array_push($this->error_messages, $error_message);
                    Log::alert($error_message);

                    $iteration += 1;
                    continue;
                }

                DB::commit();
                Log::info('Berhasil create produk '. $product->name . ' row: ' . $iteration + 1);
                $iteration += 1;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::critical('something went wrong when import product bulk: '. $e->getMessage());
        }




    }

    /**
     * serialized have_stock
     * @param string $apapun
     * @return bool
     */
    private function have_stock($apapun) {
        return (strtolower($apapun) == 'ya') ? true : false;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

}
