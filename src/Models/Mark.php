<?php
namespace Veneridze\LaravelQuestion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model {
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'quiz_marks';

    public function quiz(): BelongsTo {
        return $this->belongsTo(Quiz::class);
    }
}