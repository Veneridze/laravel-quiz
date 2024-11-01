<?php
namespace Veneridze\LaravelQuestion\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use \Illuminate\Support\Facades\Auth;

class Attempt extends Model {
    protected $guarded = [];
    protected $casts = [
        'ended_at' => 'datetime'
    ];
    protected $appends = [
        'mark', 'correctAnswersCount', 'wrongAnswersCount', 'isActive', 'time'
    ];
    public function quiz(): BelongsTo {
        return $this->belongsTo(Quiz::class);
    }

    public function getTimeAttribute(): int | null {
        return !$this->isActive ? Carbon::parse($this->created_at)->diffInSeconds(Carbon::parse($this->ended_at)) : null;
    }

    public function getisActiveAttribute() {
        
        return is_null($this->ended_at);
        //return is_null($this->ended_at) || (is_null($this->quiz->time) || Carbon::parse($this->ended_at)->diffInSeconds() < $this->quiz->time);
    }

    public function finish() {
        $this->update([
            'ended_at' => Carbon::now()
        ]);
    }

    public function answers(): HasMany {
        return $this->hasMany(Answer::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(Auth::getConfig('model'));
    }

    public function mark(): string {
        $correct = $this->correctAnswersCount;
        if($correct < $this->quiz->minimal) {
            return 'bad';
        } else

        if($correct >= $this->quiz->minimal && $correct < $this->quiz->good) {
            return 'minimal';
        } else

        if($correct >= $this->quiz->good && $correct < $this->quiz->perfect) {
            return 'good';
        } else

        if($correct >= $this->quiz->perfect) {
            return 'perfect';
        } else {
            return 'unknown';
        }

        //return $this->quiz->mark()->where('correct_count', '<=', $this->correctAnswersCount)
        //->orderBy('correct_count')
        //->first();
    }

    public function isPassed(): bool {
        return $this->mark() != 'bad';// ? true : false;
    }

    public function getMarkAttribute(): string {
        return $this->mark();
    }

    public function getCorrectAnswersCountAttribute() {
        return $this->answers->filter(fn(Answer $answer) => $answer->isCorrect)->count();
    }

    public function getWrongAnswersCountAttribute() {
        return $this->answers->filter(fn(Answer $answer) => !$answer->isCorrect)->count();
    }
}