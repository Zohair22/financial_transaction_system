<?php

namespace Modules\Plaid\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchPlaidAccountsRequest extends FormRequest
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
            'access_token' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'access_token.required' => 'A Plaid access token is required.',
        ];
    }
}
