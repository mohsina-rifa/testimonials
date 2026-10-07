<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $space_id
 * @property string $submitter_name
 * @property string $submitter_email
 * @property string|null $company_name
 * @property string|null $social_link
 * @property string|null $profile_photo_path
 * @property string|null $profile_photo_url
 * @property string $testimonial_text
 * @property int|null $rating
 * @property bool $consent_given
 * @property bool $is_favorite
 * @property bool $is_wall_of_love
 * @property bool $is_hidden
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'submitter_name', 'submitter_email', 'company_name', 'social_link', 'profile_photo_path',
    'testimonial_text', 'rating', 'consent_given', 'is_favorite', 'is_wall_of_love', 'is_hidden',
])]
class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'consent_given' => false,
        'is_favorite' => false,
        'is_wall_of_love' => false,
        'is_hidden' => false,
    ];

    protected static function booted(): void
    {
        static::saving(function (Testimonial $testimonial): void {
            if ($testimonial->rating !== null && ($testimonial->rating < 1 || $testimonial->rating > 5)) {
                throw ValidationException::withMessages(['rating' => 'The rating must be between 1 and 5.']);
            }

            if ($testimonial->is_wall_of_love && ! $testimonial->consent_given) {
                throw ValidationException::withMessages([
                    'is_wall_of_love' => 'A testimonial without consent cannot be added to the Wall of Love.',
                ]);
            }
        });

        static::deleted(function (Testimonial $testimonial): void {
            if ($testimonial->profile_photo_path !== null) {
                Storage::disk('public')->delete($testimonial->profile_photo_path);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'consent_given' => 'boolean',
            'is_favorite' => 'boolean',
            'is_wall_of_love' => 'boolean',
            'is_hidden' => 'boolean',
        ];
    }

    /**
     * @return Attribute<string, string>
     */
    protected function submitterEmail(): Attribute
    {
        return Attribute::set(fn (string $value): string => mb_strtolower(trim($value)));
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->profile_photo_path === null
            ? null
            : Storage::disk('public')->url($this->profile_photo_path));
    }

    /**
     * Restrict the query to testimonials that may be shown publicly.
     *
     * @param  Builder<Testimonial>  $query
     */
    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('is_wall_of_love', true)
            ->where('is_hidden', false)
            ->where('consent_given', true);
    }

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
