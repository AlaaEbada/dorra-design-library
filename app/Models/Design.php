<?php

namespace App\Models;

use App\Enums\DesignStatus;
use Database\Factories\DesignFactory;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
