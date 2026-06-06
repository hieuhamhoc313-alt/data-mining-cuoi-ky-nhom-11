<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area' => 'required|numeric|min:1|max:10000',
            'frontage' => 'nullable|numeric|min:0|max:1000',
            'access_road' => 'nullable|numeric|min:0|max:1000',
            'floors' => 'nullable|integer|min:1|max:100',
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'legal_status' => 'required|string|in:Have certificate,Sale contract,Pending,Other',
            'furniture_state' => 'nullable|string|in:Full,Basic,Empty',
            'city' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'area.required' => 'Diện tích là bắt buộc.',
            'area.numeric' => 'Diện tích phải là số.',
            'area.min' => 'Diện tích phải lớn hơn 0.',
            'legal_status.required' => 'Tình trạng pháp lý là bắt buộc.',
            'legal_status.in' => 'Tình trạng pháp lý không hợp lệ.',
        ];
    }
}
