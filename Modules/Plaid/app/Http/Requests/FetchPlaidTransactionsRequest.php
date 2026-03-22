<?php

namespace Modules\Plaid\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchPlaidTransactionsRequest extends FormRequest
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
            'start_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['sometimes', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'access_token.required' => 'A Plaid access token is required.',
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }
}
