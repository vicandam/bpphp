<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegistrationRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'products_to_sell' => 'nullable|string|max:255',
            'product_category' => 'required|in:Non-food,Food & Drinks',
            'contact_person_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile_number' => 'nullable|string|max:50',
            'birthday' => 'nullable|date',
            'office_address' => 'nullable|string|max:500',
        ];
    }
}
