<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Finance');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_date' => ['required', 'date'],
            'reference_number' => ['required', 'string', 'max:100', 'unique:payments,reference_number'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
            'reference_number.required' => 'Nomor referensi pembayaran (no. bank) wajib diisi.',
            'reference_number.unique' => 'Nomor referensi pembayaran sudah pernah digunakan.',
            'notes.max' => 'Catatan pembayaran maksimal 1000 karakter.',
        ];
    }
}
