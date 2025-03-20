<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string',
            'platform' => 'required|string|in:web,mobile',
            'user_agent' => Rule::requiredIf($this->input('platform') === 'web'),
            'device_id' => Rule::requiredIf($this->input('platform') === 'mobile'),
        ];
    }
}
