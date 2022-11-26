<?php

namespace App\Models;

use App\Helper\Generator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->product_code = Generator::virtualProductCode();
            $model->slug = Str::slug($model->name);
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->updated_by = Auth::id();
        });
    }

    protected $guarded = [];

    /**
     * Get all of the product_categories for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    /**
     * Get all of the product_prices for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get all of the product_stocks for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }
}
