<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'alamat_id' => ['nullable','string'],
            'metode_pembayaran' => ['nullable','string','max:50'],
            'catatan' => ['nullable','string','max:500'],
            'manual_bank' => ['nullable','string','max:50'],
        ];
    }
}
