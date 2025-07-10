<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->vendor === null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:vendors',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:vendors',
        ];
    }
}
