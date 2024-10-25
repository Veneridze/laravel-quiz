<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model {
    protected $guarded = [];
    //protected $casts = [
    //    'isCorrect' => 'boolean'
    //];

    public function attempt(): BelongsTo {
        return $this->belongsTo(Attempt::class);
    }
    public function getIsCorrectAttribute(): bool {
        return true;
    }
    public function option(): BelongsTo {
        return $this->belongsTo(Option::class);
    }
}