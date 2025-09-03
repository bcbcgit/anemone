<?php

namespace App\Http\Requests;

use App\Models\Scenario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScenarioRequest extends FormRequest
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
        // ルートモデルバインディング {scenario} を取得（id でも Model でも対応）
        $param = $this->route('scenario');
        $id = $param instanceof Scenario ? $param->getKey()
            : (is_numeric($param) ? (int) $param : null);

        return [
            'title'   => ['nullable', 'string', 'max:255'],
            'url'     => [
                'required', 'url', 'max:2048',
                Rule::unique('scenarios', 'url')->ignore($id), // ← 自分を除外
            ],
            'body'    => ['nullable', 'string'],
            'visible' => ['required', 'in:0,1'],

            // 画像は任意、最大10MB（KB単位）。必要なら dimensions や mimes を調整してください
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:10240', // 10MB
                // 'dimensions:min_width=100,min_height=100,max_width=3000,max_height=3000',
            ],

            // シナリオ種別（多対多）
            'kinds'   => ['nullable', 'array'],
            'kinds.*' => ['integer', 'exists:kinds,id'],

            // シナリオ要素（多対多）
            'elements'   => ['nullable', 'array'],
            'elements.*' => ['integer', 'exists:elements,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kinds'   => 'シナリオ種別',
            'kinds.*' => 'シナリオ種別',
            'elements'   => 'シナリオ要素',
            'elements.*' => 'シナリオ要素',
        ];
    }
}
