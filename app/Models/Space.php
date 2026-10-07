<?php

namespace App\Models;

use App\Enums\SpaceField;
use App\Enums\Theme;
use Database\Factories\SpaceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * @property int $id
 * @property int $user_id
 * @property string $public_id
 * @property string $title
 * @property string $subtitle
 * @property string $ask
 * @property string $slug
 * @property Theme $theme
 * @property bool $rating_enabled
 * @property array<string, array{enabled: bool, required: bool}> $field_configuration
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['title', 'subtitle', 'ask', 'slug', 'theme', 'rating_enabled', 'field_configuration'])]
class Space extends Model
{
    /** @use HasFactory<SpaceFactory> */
    use HasFactory, HasUlids;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'theme' => 'light',
        'rating_enabled' => true,
        'field_configuration' => '[]',
    ];

    protected static function booted(): void
    {
        static::updating(function (Space $space): void {
            $space->public_id = $space->getOriginal('public_id');
        });

        static::created(function (Space $space): void {
            $space->embedConfiguration()->create();
        });

        static::deleting(function (Space $space): void {
            $space->testimonials()->lazyById()->each(fn (Testimonial $testimonial) => $testimonial->delete());
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'theme' => Theme::class,
            'rating_enabled' => 'boolean',
            'field_configuration' => 'array',
        ];
    }

    /**
     * Determine whether an optional field is shown on the collection form.
     */
    public function isFieldEnabled(SpaceField $field): bool
    {
        return $this->field_configuration[$field->value]['enabled'] ?? false;
    }

    /**
     * Determine whether an optional field must be filled in.
     */
    public function isFieldRequired(SpaceField $field): bool
    {
        return $this->isFieldEnabled($field) && ($this->field_configuration[$field->value]['required'] ?? false);
    }

    /**
     * Store field configuration, rejecting keys outside the supported fields.
     *
     * @param  array<string, array{enabled?: bool, required?: bool}>  $configuration
     */
    public function setFieldConfigurationAttribute(array $configuration): void
    {
        $unknownKeys = array_diff(array_keys($configuration), array_column(SpaceField::cases(), 'value'));

        if ($unknownKeys !== []) {
            throw new InvalidArgumentException('Unsupported field configuration keys: '.implode(', ', $unknownKeys));
        }

        $this->attributes['field_configuration'] = json_encode(array_map(fn (array $field): array => [
            'enabled' => (bool) ($field['enabled'] ?? false),
            'required' => (bool) ($field['required'] ?? false),
        ], $configuration));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Testimonial, $this>
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * @return HasOne<EmbedConfiguration, $this>
     */
    public function embedConfiguration(): HasOne
    {
        return $this->hasOne(EmbedConfiguration::class);
    }
}
