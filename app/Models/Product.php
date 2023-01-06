<?php

namespace App\Models;

use App\Helper\Generator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->product_code = Generator::virtualProductCode();
            $model->slug = Str::slug($model->name) . '-' . $model->outlet_id;
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name) . '-' . $model->outlet_id;
            $model->updated_by = Auth::id();
        });
    }

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'have_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The categories that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    /**
     * Get all of the product_prices for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product_price(): HasOne
    {
        return $this->hasOne(ProductPrice::class)->latest();
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

    /**
     * Get the outlet that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
