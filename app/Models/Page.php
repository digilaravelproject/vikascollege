<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Page
 *
 * @package App\Models
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property mixed $content
 * @property string|null $image
 * @property string|null $pdf
 * @property int|null $menu_id
 * @property bool $status
 * @property \App\Models\Menu|null $menu
 */
class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'image',
        'pdf',
        'menu_id',
        'status',
    ];

    protected $casts = [
        'content' => 'json',
        'status' => 'boolean',
    ];

    /**
     * Use slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Linked menu item (optional).
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Ensure slug is always lowercase and trimmed.
     */
    public function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = strtolower(trim($value, '/'));
    }

    /**
     * Ensure title is properly capitalized.
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = ucwords(trim($value));
    }

    /**
     * Generate the public URL for this page.
     */
    public function getLinkAttribute(): string
    {
        return route('page.view', $this->slug);
    }

    /**
     * Short preview of content (useful for admin lists).
     */
    public function getExcerptAttribute(): string
    {
        $text = is_array($this->content) ? json_encode($this->content) : strip_tags((string) $this->content);
        return mb_strimwidth($text, 0, 120, '...');
    }
}
