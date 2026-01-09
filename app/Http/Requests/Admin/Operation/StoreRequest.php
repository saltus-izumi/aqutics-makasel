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
                'required_unless:is_draft,1'
            ],
            'investment_id' => [
                'required_unless:is_draft,1'
            ],
            'investment_room_id' => [
                'required_unless:is_draft,1'
            ],
            'operation_template_id' => [
                'required_unless:is_draft,1'
            ],
            'title' => [
                'required_unless:is_draft,1'
            ],
        ];
    }

    public function messages()
    {
        return [
            'title.required_unless' => ':attributeを入力してください。',
            '*.required_unless' => ':attributeを選択してください。',
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
