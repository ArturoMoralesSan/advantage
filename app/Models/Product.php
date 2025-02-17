<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $appends = ['cut_name'];

    public function getCutNameAttribute()
    {
        return isset($this->pivot->cut_id) ? Cut::where('id', $this->pivot->cut_id)->value('name') : null;
    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Get the section that owns the types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the section that owns the measure.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_products')
            ->withPivot(['cut_id', 'width', 'height', 'base_price', 'profit_percentage', 'sale_price','quantity_product'])
            ->withTimestamps();
    }
    


    /**
     * Get the links that belong to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
