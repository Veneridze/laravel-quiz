<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model {
    protected $casts = [
        'isCorrect' => 'boolean'
    ];

    public function attempt(): BelongsTo {
        return $this->belongsTo(Attempt::class);
    }

    public function getIsCorrectAttribute(): bool {
        return true;
    }

    public function question(): BelongsTo {
        return $this->belongsTo(Question::class);
    }
}