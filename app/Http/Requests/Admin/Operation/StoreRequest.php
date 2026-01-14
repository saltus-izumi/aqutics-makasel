<?php

namespace App\Http\Requests\Admin\Operation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'owner_id' => [
                'required'
            ],
            'investment_id' => [
                'required'
            ],
            'investment_room_id' => [
                'required'
            ],
            'operation_template_id' => [
                'required'
            ],
            'title' => [
                'required'
            ],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => ':attributeを入力してください。',
            '*.required' => ':attributeを選択してください。',
        ];
    }

    public function attributes()
    {
        return [
            'owner_id' => 'オーナー',
            'investment_id' => '物件',
            'investment_room_id' => '部屋',
            'operation_template_id' => 'カテゴリ',
        ];
    }
}
