<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->hasAnyRole(['Supervisor', 'Manager', 'Director']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:Approve,Reject'],
            'notes' => ['required_if:action,Reject', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Tindakan approval wajib ditentukan.',
            'action.in' => 'Tindakan approval tidak valid.',
            'notes.required_if' => 'Catatan (notes) wajib diisi apabila Anda menolak pengajuan.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
