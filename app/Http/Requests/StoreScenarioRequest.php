<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScenarioRequest extends FormRequest
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
            'title'   => ['nullable','string','max:255'],
            'url'     => ['required','url','max:2048','unique:scenarios,url'],
            'body'    => ['nullable','string'],
            'visible' => ['required','in:0,1'],

            // 画像（KB 指定。5MBなら 5120 を推奨）
            'image' => [
                'nullable',
                'image', // ファイル＋画像判定まで含むので file は省略可
                'mimes:jpeg,jpg,png,webp',
                'max:10240',
                //'dimensions:min_width=100,min_height=100,max_width=3000,max_height=3000',
            ],

            'kinds'   => ['nullable','array'],
            'kinds.*' => ['integer','exists:kinds,id'],

            'elements'   => ['nullable','array'],
            'elements.*' => ['integer','exists:elements,id'],
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
