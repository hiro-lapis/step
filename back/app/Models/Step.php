<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'achievement_time_type_id',
    ];

    /** accessor */
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

    /** query */

    /**
     * マスタ情報テーブルとのjoin
     *
     * @param  $query
     * @return $query Builder
     */
    public function scopeJoinUsers($query): Builder
    {
        return $query->join('users', 'users.id', '=', 'steps.user_id');
    }

    /**
     * マスタ情報テーブルとのjoin
     *
     * @param  $query
     * @return $query Builder
     */
    public function scopeJoinMasterTables($query): Builder
    {
        return $query->join('categories', 'categories.id', '=', 'steps.category_id')
            ->join('achievement_time_types', 'achievement_time_types.id', '=', 'steps.achievement_time_type_id');
    }
}
