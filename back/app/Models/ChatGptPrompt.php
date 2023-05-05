<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGptPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_type',
        'success',
        'prompt',
        'response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
