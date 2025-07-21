<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'mobile_no' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date'],
            'city_or_province' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'most_favorite_film' => ['nullable', 'string', 'max:255'],
            'most_favorite_song' => ['nullable', 'string', 'max:255'],
            'greatest_dream' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            dd('Validation failed', $validator->errors()->toArray(), $this->all());
        }
    }

}
