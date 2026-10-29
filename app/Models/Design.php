<?php

namespace App\Models;

use App\Enums\DesignStatus;
use Database\Factories\DesignFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Design extends Model
{
    /** @use HasFactory<DesignFactory> */
    use HasFactory;

    protected $fillable = ['title', 'category_id', 'status', 'thumbnail'];

    protected function casts(): array
    {
        return [
            'status' => DesignStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['title']) && $filters['title'] !== '') {
            // ! is portable across MySQL SQL modes; backslashes remain literal.
            $prefix = strtr($filters['title'], ['!' => '!!', '%' => '!%', '_' => '!_']);
            $query->whereRaw("title LIKE ? ESCAPE '!'", [$prefix.'%']);
        }
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
