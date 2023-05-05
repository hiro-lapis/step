<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGptUsageInformation extends Model
{
    use HasFactory;

    /**
     * 1日のchatGPTAPI利用回数上限
     */
    public const LIMIT_PER_DAY = 100;

    protected $fillable = [
        'user_id',
        'date',
        'usage_count',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'date' => 'date',
        'usage_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
