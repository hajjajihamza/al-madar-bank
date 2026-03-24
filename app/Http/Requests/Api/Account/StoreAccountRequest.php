<?php

namespace App\Http\Requests\Api\Account;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class StoreAccountRequest extends FormRequest
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
            'type' => ['required', new Enum(AccountType::class)],
            'guardian_id' => ['nullable', 'exists:users,id']
        ];
    }

    /**
     * Configure the validator instance.
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            $age = $user->age ?? 0;
            $type = $this->type;

            if ($type === AccountType::MINEUR->value) {
                if ($age >= 18) {
                    $validator->errors()->add('type', 'The MINEUR account type is only available for users under 18.');
                }

                if (!$this->filled('guardian_id')) {
                    $validator->errors()->add('guardian_id', 'The guardian_id field is required when the account type is MINEUR.');
                }
            } else {
                if ($age < 18) {
                    $validator->errors()->add('type', 'You are not allowed to create an account with this type. Try to create account type MINEUR with guardian please');
                }
            }
        });
    }
}
