<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\ChallengeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'challenge_user_id',
        'challenged_at',
        'cleared_at',
        'status', // ChallengeStep::STATUS
        // チャレンジ時のステップ情報
        'step_id',
        'post_user_id',
        'category_id',
        'achievement_time_type_id',
        'name',
        'image_url',
        'summary',
        'merit',
    ];

    /**
     * モデルの「起動」メソッド
     *
     * @return void
     */
    protected static function booted()
    {
        // 新しいステップへのチャレンジ時に、チャレンジ情報を登録更新
        static::created(function (ChallengeStep $challenge_step) {
            ChallengeInformation::updateOrCreate(
                [ 'user_id' => $challenge_step->challenge_user_id ],
                [
                    'challenge_count' => ChallengeStep::where('challenge_user_id', $challenge_step->challenge_user_id)->count(),
                    'challenging_count' => ChallengeStep::where('challenge_user_id', $challenge_step->challenge_user_id)->challenging()->count(),
                ]
            );
        });
        // 新しいステップへのチャレンジ状況更新時に、チャレンジ情報を登録更新
        static::updated(function (ChallengeStep $challenge_step) {
            ChallengeInformation::updateOrCreate(
                [ 'user_id' => $challenge_step->challenge_user_id ],
                [
                    'challenge_count' => ChallengeStep::challengeUserId($challenge_step->challenge_user_id)->count(),
                    'challenging_count' => ChallengeStep::challengeUserId($challenge_step->challenge_user_id)->challenging()->count(),
                    'clear_count' => ChallengeStep::challengeUserId($challenge_step->challenge_user_id)->cleared()->count(),
                    'fail_count' => ChallengeStep::challengeUserId($challenge_step->challenge_user_id)->failed()->count(),
                ]
            );
        });
    }

    /** relation */

    /**
     * @return BelongsTo
     */
    public function challengeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'challenge_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function postUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'post_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function achievementTimeType(): BelongsTo
    {
        return $this->belongsTo(AchievementTimeType::class);
    }

    /** scope */

    public function scopeChallengeUserId($query, int $user_id)
    {
        return $query->where('challenge_user_id', $user_id);
    }

    public function scopeChallenging($query)
    {
        return $query->where('status', ChallengeStatusEnum::Challenging);
    }

    public function scopeCleared($query)
    {
        return $query->where('status', ChallengeStatusEnum::Cleared);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', ChallengeStatusEnum::Failed);
    }
}
