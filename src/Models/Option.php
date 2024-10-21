<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model {
    public $table = 'question_options';

    public function question(): BelongsTo {
        return $this->belongsTo(Question::class);
    }
}