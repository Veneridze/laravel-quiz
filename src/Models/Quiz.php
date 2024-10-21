<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model {

    public function questions(): HasMany {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasMany {
        return $this->hasMany(Answer::class);
    }

    public function attempts(): HasMany {
        return $this->hasMany(Attempt::class);
    }

    public function getActiveAttempt(Model $user): Attempt | null {
        $attempt = $this
            ->attempts()
            ->whereBelongsTo($user)
            ->whereNull('finished_at')
            ->first();

        return $attempt && $attempt->isActive() ? $attempt : null;
    }
}