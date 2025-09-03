<?php

namespace App\Http\Requests;

use App\Models\Kind;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateElementRequest extends FormRequest
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
        // ルートパラメータ {kind}（モデル or 数値）
        $param = $this->route('kind');
        $id = $param instanceof Kind ? $param->getKey()
            : (is_numeric($param) ? (int) $param : null);

        return [
            'title'   => ['required','string','max:100',
                Rule::unique('kinds','title')->ignore($id) // ← 自分のIDを無視
            ],
            'visible' => ['required','in:0,1'],
        ];
    }

    public function attributes(): array {
        return [
            'title'=>'シナリオ要素',
            'visible'=>'表示設定'
        ];
    }
}
