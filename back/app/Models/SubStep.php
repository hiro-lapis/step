<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'name',
        'detail',
        'image_url',
        'sort_number',
    ];

    /** accessor */
    /** relation */

    /**
     * @return BelongsTo
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    /** query */
}
