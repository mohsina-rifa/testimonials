<?php

namespace App\Models;

use App\Enums\EmbedLayout;
use Database\Factories\EmbedConfigurationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $space_id
 * @property EmbedLayout $layout
 * @property bool $dark_mode
 * @property bool $animation_enabled
 * @property string $background_color
 * @property bool $show_rating
 * @property bool $show_company
 * @property bool $show_profile_photo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['layout', 'dark_mode', 'animation_enabled', 'background_color', 'show_rating', 'show_company', 'show_profile_photo'])]
class EmbedConfiguration extends Model
{
    /** @use HasFactory<EmbedConfigurationFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'layout' => 'masonry',
        'dark_mode' => false,
        'animation_enabled' => true,
        'background_color' => '#ffffff',
        'show_rating' => true,
        'show_company' => true,
        'show_profile_photo' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'layout' => EmbedLayout::class,
            'dark_mode' => 'boolean',
            'animation_enabled' => 'boolean',
            'show_rating' => 'boolean',
            'show_company' => 'boolean',
            'show_profile_photo' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
