<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementTimeType extends Model
{
    use HasFactory;

    // マスタデータなのでアプリケーション側からの更新なし
    protected $fillable = [];

    /** accessor */
    /** relation */
}
