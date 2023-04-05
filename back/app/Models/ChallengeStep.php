<?php declare(strict_types=1);

namespace App\Models;

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
}
