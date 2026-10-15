<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePoojaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $poojaId = $this->route('pooja') ? $this->route('pooja')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:pooja_categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'significance' => ['nullable', 'string'],
            'procedure' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'samagri_list' => ['nullable', 'array'],
            'samagri_list.*' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_popular' => ['nullable', 'boolean'],
            'is_temple_pooja' => ['nullable', 'boolean'],
            'primary_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'delete_media_ids' => ['nullable', 'array'],
            'delete_media_ids.*' => ['integer', 'exists:pooja_media,id'],
            'pricings' => ['required', 'array', 'min:1'],
            'pricings.*.id' => ['nullable', 'integer'],
            'pricings.*.title' => ['required', 'string', 'max:255'],
            'pricings.*.price' => ['required', 'numeric', 'min:0'],
            'pricings.*.special_price' => ['nullable', 'numeric', 'min:0'],
            'pricings.*.description' => ['nullable', 'string', 'max:500'],
            'pricings.*.includes_samagri' => ['nullable', 'boolean'],
        ];
    }
}