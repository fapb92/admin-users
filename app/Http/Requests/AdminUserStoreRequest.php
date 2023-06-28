<?php

namespace App\Http\Requests;

use App\Models\Role;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminUserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'role' => [
                'required',
                'exists:roles,key',
                function (string $attribute, mixed $value, Closure $fail) {
                    $rolToAdd = Role::firstWhere('key', $value);
                    $user = $this->user();
                    if ($rolToAdd->priority < $user->getActiveRole()->priority) {
                        $fail("The {$attribute} is invalid.");
                    }
                },
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'password' => "123"
        ]);
    }
}
