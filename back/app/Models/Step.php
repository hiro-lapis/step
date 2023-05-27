<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'image_url',
        'name',
        'summary',
        'achievement_time_type_id',
        'time_count',
        'is_active',
        'draft',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y年n月j日',
        'updated_at' => 'datetime:Y年n月j日',
        'deleted_at' => 'datetime:Y年n月j日',
        'draft'      => 'object',
        'is_active'  => 'bool',
    ];

    protected $appends = [
        'achievement_time',
        'is_challenged',
        'is_cleared',
    ];

    public const DEFAULT_IMAGE_URL = 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/step-card-default.png';

    /** mutator */

    /**
     * 画像未設定の時はデフォルトのURLを設定
     *
     * @return Attribute
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? self::DEFAULT_IMAGE_URL,
            set: fn ($value) => $value,
        );
    }

    /**
     * ステップ名未設定の時は名称未設定として表示
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => empty($value) ? '(名称未設定)' : $value,
        );
    }

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

    public function getAchievementTimeAttribute(): string
    {
        return isset($this->achievementTimeType->display_name)
            ? ($this->time_count . $this->achievementTimeType->display_name) ?? '' : '';
    }

    public function getOgpImageUrlAttribute(): string
    {
        return $this->image_url ?? 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/ogp-default.png';
    }

    public function getOgpSummaryAttribute(): string
    {
        return !empty($this->summary) ? $this->summary : $this->user_name . 'さんが書いた' . $this->category_name . 'についてのステップです';
    }

    public function getIsChallengedAttribute(): bool
    {
        if (auth()->guest()) return false;
        $query = ChallengeStep::query();
        return $query->stepId($this->id)
            ->challengeUserId(auth()->user()->id)
            ->challenging()
            ->exists();
    }

    public function getIsClearedAttribute(): bool
    {
        if (auth()->guest()) return false;
        $query = ChallengeStep::query();
        return $query->stepId($this->id)
            ->challengeUserId(auth()->user()->id)
            ->cleared()
            ->exists();
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

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('steps.is_active', true);
    }
}
