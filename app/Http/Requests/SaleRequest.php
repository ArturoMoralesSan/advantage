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
        return [

            'product_name' => ['required', new NotUppercase, new NotLowercase, 'max:100'],
            'user_id'     => 'required|max:20',
            'type_id'     => 'required|max:20',
            'product_id'  => 'required|max:20',
            'cut_id'      => 'required|max:20',
            'width'       => 'required|max:6',
            'height'      => 'required|max:6',
            'base_price'  => 'required|max:10',
            'profit_percentage' => 'required|max:5',
            
        ];
    }
}
