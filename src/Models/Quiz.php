<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Quiz extends Model {

    protected $guarded = [];
    public function model(): MorphTo {
        return $this->morphTo('model');
    }
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
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        return $attempt && $attempt->isActive ? $attempt : null;
    }
}