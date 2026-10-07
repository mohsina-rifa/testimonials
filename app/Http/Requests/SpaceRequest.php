<?php

namespace App\Http\Requests;

use App\Enums\SpaceField;
use App\Enums\Theme;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpaceRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'ask' => ['required', 'string', 'max:1000'],
            'slug' => [
                'required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('spaces', 'slug')->ignore($this->route('space')),
            ],
            'theme' => ['required', Rule::enum(Theme::class)],
            'rating_enabled' => ['required', 'boolean'],
        ];

        foreach (SpaceField::cases() as $field) {
            $rules["field_configuration.{$field->value}.enabled"] = ['required', 'boolean'];
            $rules["field_configuration.{$field->value}.required"] = ['required', 'boolean'];
        }

        return $rules;
    }
}
