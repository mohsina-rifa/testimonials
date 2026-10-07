<?php

namespace App\Http\Requests;

use App\Enums\SpaceField;
use App\Models\Space;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitTestimonialRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Space $space */
        $space = $this->route('space');

        return [
            'submitter_name' => ['required', 'string', 'max:255'],
            'submitter_email' => ['required', 'email', 'max:255'],
            'testimonial_text' => ['required', 'string', 'max:2000'],
            'rating' => [$space->rating_enabled ? 'required' : 'nullable', 'integer', 'between:1,5'],
            'consent_given' => ['accepted'],
            'company_name' => $this->optionalFieldRules($space, SpaceField::Company, ['string', 'max:255']),
            'social_link' => $this->optionalFieldRules($space, SpaceField::SocialLink, ['url', 'max:255']),
            'profile_photo' => $this->optionalFieldRules($space, SpaceField::ProfilePhoto, ['image', 'max:2048']),
        ];
    }

    /**
     * @param  list<string>  $constraints
     * @return list<string>
     */
    private function optionalFieldRules(Space $space, SpaceField $field, array $constraints): array
    {
        if (! $space->isFieldEnabled($field)) {
            return ['prohibited'];
        }

        return [$space->isFieldRequired($field) ? 'required' : 'nullable', ...$constraints];
    }
}
