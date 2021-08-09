<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => ['required', 'phone:RU'],
            'name' => ['required'],
            'last_name' => ['required'],
            'email' => ['email', 'required'],
            'birthday' => ['required', 'date_format:Y-m-d'],
            'service_id' => ['exists:services,id', 'required'],
        ];
    }

    // Кастомное сообщения для телефона
    public function messages(): array
    {
        return [
            'phone.phone' => 'Номер телефона в неверном формате'
        ];
    }
}
