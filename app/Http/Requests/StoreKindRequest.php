<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKindRequest extends FormRequest
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
            //
            'title'    => ['required','string','max:255','unique:kinds,title'],
            'visible' => ['required','in:0,1'],
        ];
    }

    public function attributes(): array {
        return [
            'title'=>'シナリオ種別',
            'visible'=>'表示設定'
        ];
    }
}
