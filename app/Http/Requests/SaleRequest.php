<?php

namespace App\Http\Requests;
use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'user_id' => 'required|max:20',
        ];

        for($i = 1; $i <= $this->products_count; $i++) {
            
            $rules['product' . $i . '_product_name'] = 'required|max:80';
            $rules['product' . $i . '_type_id']      = 'required';
            $rules['product' . $i . '_product_id']   = 'required';
            $rules['product' . $i . '_cut_id']       = 'required';
            $rules['product' . $i . '_width']        = 'required';
            $rules['product' . $i . '_height']       = 'required';
            $rules['product' . $i . '_quantity_product'] = 'required';
            $rules['product' . $i . '_base_price']   = 'required';
            $rules['product' . $i . '_profit_percentage'] = 'required';
        }

        return $rules;
    }
}
