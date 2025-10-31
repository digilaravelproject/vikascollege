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
 * @property-read string $link
 * @property-read string $statusLabel
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
     * ACCESSORS & MUTATORS
     * ============================
     */

    public function getTitleAttribute(?string $value): string
    {
        return ucwords($value ?? '');
    }

    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = preg_replace('/\s+/', ' ', trim($value));
    }

    /**
     * ============================
     * RELATIONSHIPS
     * ============================
     */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('status', true)
            ->orderBy('order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function page(): HasOne
    {
        return $this->hasOne(Page::class, 'menu_id', 'id');
    }

    /**
     * ============================
     * SCOPES
     * ============================
     */

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * ============================
     * HELPERS
     * ============================
     */

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * ===============================================
     * UPDATED/MERGED LINK FUNCTION
     * ===============================================
     *
     * Generate a proper front-end link.
     * Yeh function ab har condition ko handle karta hai.
     */
    public function getLinkAttribute(): string
    {
        // === Priority 1: Agar Page se link hai (Best Case) ===
        // Agar admin ne menu ko Page Builder ke 'Page' se joda hai,
        // toh hamesha ussi ka route istemal karo. Yeh sabse safe hai.
        if ($this->page) {
            return route('page.view', $this->page->slug);
        }

        // === Priority 2: Agar Page se link nahi hai, toh 'url' field check karo ===
        $url = $this->url;

        // Agar URL field khaali hai, toh '#' return karo
        if (empty($url) || $url === '#') {
            return '#';
        }

        // 1. Agar poora URL hai (http/https), toh waisa hi return karo
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        // 2. Agar manual absolute path hai (jaise /contact), toh use absolute banao
        if (str_starts_with($url, '/')) {
            return url($url); // url('/contact') -> http://.../contact
        }

        // 3. (Aapka Fix) Agar kuch aur hai (jaise 'abc'),
        // toh maano ki yeh ek page slug hai aur use 'page.view' route se banao.
        // Yeh aapki relative path wali problem ko 100% fix kar dega.
        return route('page.view', ['slug' => $url]);
    }

    /**
     * Get a human-readable status name.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
