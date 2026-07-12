<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('submission.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'requested_amount' => ['required', 'numeric', 'min:1000', 'max:10000000000'],
            'description' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB per file
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori pengajuan wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'requested_amount.required' => 'Nominal pengajuan wajib diisi.',
            'requested_amount.numeric' => 'Nominal pengajuan harus berupa angka.',
            'requested_amount.min' => 'Nominal pengajuan minimal Rp 1.000.',
            'description.required' => 'Deskripsi pengajuan wajib diisi.',
            'attachments.*.mimes' => 'Format file pendukung harus berupa PDF, JPG, JPEG, atau PNG.',
            'attachments.*.max' => 'Ukuran file pendukung maksimal 5 MB.',
        ];
    }
}
