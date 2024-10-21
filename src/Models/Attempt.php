<?php
namespace Veneridze\LaravelQuestion\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Attempt extends Model {

    protected $casts = [
        'finished_at' => 'datetime'
    ];
    protected $appends = [
        'mark', 'correctAnswersCount', 'wrongAnswersCount', 'isActive'
    ];
    public function question(): BelongsTo {
        return $this->belongsTo(Question::class);
    }

    public function getisActiveAttribute() {
        
        return is_null($this->finished_at) && (is_null($this->question->time) || Carbon::parse($this->finished_at)->diffInSeconds() < $this->question->time);
    }

    public function answers(): HasMany {
        return $this->hasMany(Answer::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function mark(): Mark | null {
        return $this->question->quiz->mark()->where('correct_count', '<=', $this->correctAnswersCount)
        ->orderBy('correct_count')
        ->first();
    }

    public function isPassed(): bool {
        return $this->mark() ? true : false;
    }

    public function getMarkAttribute() {
        $mark = $this->mark();
        return $mark ? $mark->mark : null;
    }

    public function getCorrectAnswersAttribute() {
        return $this->answers->filter(fn(Answer $answer) => $answer->isCorrect);
    }

    public function getWrongAnswersAttribute() {
        return $this->answers->filter(fn(Answer $answer) => !$answer->isCorrect);
    }
}