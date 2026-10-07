<?php

namespace App\Http\Requests;

use App\Enums\EmbedLayout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmbedConfigurationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'layout' => ['required', Rule::enum(EmbedLayout::class)],
            'dark_mode' => ['required', 'boolean'],
            'animation_enabled' => ['required', 'boolean'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'show_rating' => ['required', 'boolean'],
            'show_company' => ['required', 'boolean'],
            'show_profile_photo' => ['required', 'boolean'],
        ];
    }
}
