<?php
namespace Veneridze\LaravelQuestion\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model {
    protected $guarded = [];
    public function quiz(): BelongsTo {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany {
        return $this->hasMany(Option::class);
    }
}