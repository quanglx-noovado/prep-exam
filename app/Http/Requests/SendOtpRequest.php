<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;

class SendOtpRequest extends FormRequest
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
            'device_token' => 'required|string',
            'send_type' => ['required', Rule::enum(OtpSendType::class)],
            'otp_purpose' => ['required', Rule::enum(OtpPurpose::class)],
        ];
    }
}
