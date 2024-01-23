<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\CustomException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;


class RegisterFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email"=> "email:rfc,dns|required",
            'password' => ['required', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new CustomException($validator->errors()->first(),400);
    }

    
}
