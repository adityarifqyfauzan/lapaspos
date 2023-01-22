<?php

namespace App\Http\Controllers\ProductManagement\Product;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Facades\Excel;

class ProductBulkInsertController extends Controller
{

    use Importable;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'outlet_id' => 'required',
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            Excel::queueImport(new ProductsImport($request->outlet_id, Auth::user()), $request->file('excel'));

            return $this->success(
                'Import Product Processing...'
            );

        } catch (\Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }
}
