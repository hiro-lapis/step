<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'summary',
        'achievement_time_type_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y年n月j日',
        'updated_at' => 'datetime:Y年n月j日',
        'deleted_at' => 'datetime:Y年n月j日',
    ];

    /** accessor */

    public function getUserNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    public function getUserImageUrlAttribute(): string
    {
        return $this->user->image_url ?? '';
    }

    public function getUserProfileAttribute(): string
    {
        return $this->user->profile ?? '';
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->category->name ?? '';
    }

    public function getAchievementTimeTypeNameAttribute(): string
    {
        return $this->achievementTimeType->name ?? '';
    }

    /**
     * 作成したユーザー自身か判定
     * 未ログイン時は一律false
     *
     * @return boolean
     */
    public function getIsWriterAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }
        return $this->user_id === auth()->user()->id;
    }

    /** relation */

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function achievementTimeType(): BelongsTo
    {
        return $this->belongsTo(AchievementTimeType::class);
    }

    /**
     * 並び順を指定して、サブステップを取得
     *
     * @return HasMany
     */
    public function subSteps(): HasMany
    {
        return $this->hasMany(SubStep::class)->orderBy('sort_number');
    }

    /**
     * チャレンジされているステップ情報を取得
     *
     * @return HasMany
     */
    public function challengeSteps(): HasMany
    {
        return $this->hasMany(ChallengeStep::class);
    }

    /** query */

    /**
     * マスタ情報テーブルとのjoin
     *
     * @param  Builder $query
     * @return $query Builder
     */
    public function scopeJoinUsers(Builder $query): Builder
    {
        return $query->join('users', 'users.id', '=', 'steps.user_id');
    }

    /**
     * マスタ情報テーブルとのjoin
     *
     * @param  Builder $query
     * @return $query Builder
     */
    public function scopeJoinMasterTables(Builder $query): Builder
    {
        return $query->join('categories', 'categories.id', '=', 'steps.category_id')
            ->join('achievement_time_types', 'achievement_time_types.id', '=', 'steps.achievement_time_type_id');
    }

    public function scopeWriterUser(Builder $query, int $user_id): Builder
    {
        return $query->where('steps.user_id', $user_id);
    }
}
