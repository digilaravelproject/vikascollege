<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Menu
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string|null $url
 * @property int|null $parent_id
 * @property int $order
 * @property bool $status
 * @property \App\Models\Page|null $page
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $children
 */
class Menu extends Model
{
    protected $fillable = [
        'title',
        'url',
        'parent_id',
        'order',
        'status',
    ];

    /**
     * ============================
     *  ACCESSORS & MUTATORS
     * ============================
     */

    /**
     * Always return title in Title Case (more readable than uppercase).
     *
     * @param  string|null  $value
     * @return string
     */
    public function getTitleAttribute(?string $value): string
    {
        return ucwords($value ?? '');
    }

    /**
     * Normalize title before saving (trim and collapse extra spaces).
     *
     * @param  string  $value
     * @return void
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = preg_replace('/\s+/', ' ', trim($value));
    }

    /**
     * ============================
     *  RELATIONSHIPS
     * ============================
     */

    /**
     * Parent menu item (if any).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Direct child menu items (active only).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('status', true)
            ->orderBy('order');
    }

    /**
     * Recursive child loader (for nested menus).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Linked page (one-to-one).
     */
    public function page(): HasOne
    {
        return $this->hasOne(Page::class, 'menu_id', 'id');
    }

    /**
     * ============================
     *  SCOPES
     * ============================
     */

    /**
     * Scope: get only top-level (main) menus.
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: only active menus.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * ============================
     *  HELPERS
     * ============================
     */

    /**
     * Check if this menu item has any active children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Generate a proper front-end link.
     *
     * If a page is linked, use its route.
     * Otherwise, use URL or "#".
     */
    public function getLinkAttribute(): string
    {
        if ($this->page) {
            return route('page.view', $this->page->slug);
        }

        return $this->url ? url($this->url) : '#';
    }

    /**
     * Get a human-readable status name.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
