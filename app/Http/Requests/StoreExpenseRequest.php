<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'vendor' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'urgency' => ['required', 'in:Low,Medium,High,Urgent'],
            'needed_date' => ['required', 'date', 'after_or_equal:today'],
        ];

        // For creation, attachment is required. For updates, it can be optional (nullable)
        if ($this->isMethod('POST')) {
            $rules['attachment'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
        } else {
            $rules['attachment'] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'expense_category_id.required' => 'Kategori pengeluaran wajib dipilih.',
            'expense_category_id.exists' => 'Kategori pengeluaran tidak valid.',
            'amount.required' => 'Nominal pengajuan wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal harus lebih dari 0.',
            'vendor.required' => 'Nama vendor wajib diisi.',
            'description.required' => 'Deskripsi pengajuan wajib diisi.',
            'urgency.required' => 'Tingkat urgensi wajib dipilih.',
            'urgency.in' => 'Tingkat urgensi tidak valid.',
            'needed_date.required' => 'Tanggal dibutuhkan wajib diisi.',
            'needed_date.date' => 'Format tanggal dibutuhkan tidak valid.',
            'needed_date.after_or_equal' => 'Tanggal dibutuhkan tidak boleh sebelum hari ini.',
            'attachment.required' => 'Dokumen lampiran pendukung wajib diunggah.',
            'attachment.file' => 'Lampiran harus berupa berkas.',
            'attachment.mimes' => 'Format lampiran wajib berupa PDF, JPG, JPEG, atau PNG.',
            'attachment.max' => 'Ukuran lampiran tidak boleh melebihi 5 MB.',
        ];
    }
}
