<?php

namespace Modules\Plaid\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetLinkTokenRequest extends FormRequest
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
            'link_token' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'link_token.required' => 'A link token is required.',
        ];
    }
}
