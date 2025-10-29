<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:Pembelian,Penjualan',
            'description' => 'nullable|string'
        ];
    }
}
