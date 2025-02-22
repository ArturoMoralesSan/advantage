<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class Inventory extends Model
{
    use HasFactory;

    public static function checkStock($inventory)
    {   
        if ($inventory->quantity <= $inventory->quantity_min) { 
            Notification::create([
                'message' => "Stock bajo: {$inventory->product->name} tiene solo {$inventory->quantity} unidades",
                'read' => false
            ]);
        }
    }

    /**
     * Get the section that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
