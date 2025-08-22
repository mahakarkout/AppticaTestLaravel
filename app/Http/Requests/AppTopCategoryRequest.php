<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppTopCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'regex:/^\d{4}-\d{2}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value);
                    if (count($parts) !== 3 || !checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                        $fail("The $attribute must be a real calendar date in the format YYYY-MM-DD.");
                    }
                }
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
