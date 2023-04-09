<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeInformation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * テーブルに関連付ける主キー
     * idカラムの代わりにuser_idカラムを主キーとして使用
     *
     * @var string
     */
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'challenge_count',
        'challenging_count',
        'clear_count',
        'fail_count',
    ];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
