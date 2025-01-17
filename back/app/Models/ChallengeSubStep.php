<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\ChallengeStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeSubStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'challenge_step_id',
        'challenged_at',
        'cleared_at',
        'status', // ChallengeStep::STATUS
        // チャレンジ時のサブステップ情報
        'sub_step_id',
        'name',
        'detail',
        'sort_number',
    ];

    protected $appends = [
        'status_name',
    ];

    public function getStatusNameAttribute(): string
    {
        return ChallengeStatusEnum::string($this->status);
    }

    /** relation */

    /**
     * @return BelongsTo
     */
    public function challengeStep(): BelongsTo
    {
        return $this->belongsTo(ChallengeStep::class);
    }

    /**
     * @return BelongsTo
     */
    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }

    public function scopeChallenging(Builder $query): Builder
    {
        return $query->where('challenge_sub_steps.status', ChallengeStatusEnum::Challenging);
    }

    public function scopeCleared(Builder $query): Builder
    {
        return $query->where('challenge_sub_steps.status', ChallengeStatusEnum::Cleared);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('challenge_sub_steps.status', ChallengeStatusEnum::Failed);
    }
}
