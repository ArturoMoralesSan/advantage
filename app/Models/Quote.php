<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $appends = ['formated_date', 'hour'];


    /**
     * Return the slugified name of the section.
     *
     * @return string
     */
    public function getFormatedDateAttribute()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }


    /**
     * Return the slugified name of the section.
     *
     * @return string
     */
    public function getHourAttribute()
    {   $created_at = $this->created_at; 
        $hora_creacion = Carbon::parse($created_at)->format('H:i A');
        return $hora_creacion;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the section that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the section that owns the types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
