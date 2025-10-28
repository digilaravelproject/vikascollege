<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Ensure menu title is always returned in uppercase.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getTitleAttribute(?string $value): string
    {
        return strtoupper($value ?? '');
    }

    /**
     * Optionally, you can also normalize title before saving.
     * For example, trimming spaces.
     *
     * @param  string  $value
     * @return void
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = trim($value);
    }

    /**
     * ============================
     *  RELATIONSHIPS
     * ============================
     */

    /**
     * Get the parent of this menu item (if any).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get the direct children (only active ones).
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('status', 1)
            ->orderBy('order');
    }

    /**
     * Recursive children loader (Main → Child → Subchild → Sub-subchild).
     * Use in controller: with('childrenRecursive')
     *
     * @return HasMany
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with([
            'childrenRecursive' => function ($query) {
                $query->orderBy('order');
            },
        ]);
    }

    /**
     * Scope to get only top-level (main) menu items.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * ============================
     *  HELPER METHODS
     * ============================
     */

    /**
     * Check if this menu item has any active children.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Generate a properly formatted URL.
     *
     * @return string
     */
    public function getLinkAttribute(): string
    {
        return $this->url ? url($this->url) : '#';
    }
}
