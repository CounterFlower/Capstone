<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRegistrationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('resident_name') && ! $this->filled('first_name')) {
            $nameParts = preg_split('/\s+/', trim((string) $this->input('resident_name')), -1, PREG_SPLIT_NO_EMPTY);

            if (count($nameParts) >= 2) {
                $this->merge([
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[count($nameParts) - 1],
                ]);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'event_id'       => ['required'],
            'first_name'     => ['required', 'string', 'max:120'],
            'middle_name'    => ['nullable', 'string', 'max:120'],
            'last_name'      => ['required', 'string', 'max:120'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'purok'          => ['nullable', 'string', 'max:60'],
        ];
    }
}