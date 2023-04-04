<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // マスタデータなのでアプリケーション側からの更新なし
    protected $fillable = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /** accessor */
    /** relation */

    /**
     * @return BelongsTo
     */
    public function steps(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
