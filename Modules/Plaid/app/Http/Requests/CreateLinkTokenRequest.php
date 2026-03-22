<?php

namespace Modules\Plaid\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLinkTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'client_user_id' => ['sometimes', 'string', 'max:255'],
            'client_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_user_id.max' => 'The client user id may not be greater than 255 characters.',
            'client_name.max' => 'The client name may not be greater than 255 characters.',
        ];
    }
}
