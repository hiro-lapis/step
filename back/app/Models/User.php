<?php
declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile',
        'image_url',
        'password',
        'skip_api_confirm',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'skip_api_confirm' => 'boolean',
    ];

    /** accessor */

    /**
     * ユーザーの画像urlを取得
     */
    public function getImageUrlAttribute($value)
    {
        return $value ?? 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/no_user_icon_img.png';
    }

    /** relation */

    /**
     * @return HasMany
     */
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    /**
     * @return HasMany
     */
    public function chatGptUsageInformations(): HasMany
    {
        return $this->hasMany(ChatGptUsageInformation::class);
    }

    /**
     * @return HasMany
     */
    public function chatGptPrompts(): HasMany
    {
        return $this->hasMany(ChatGptPrompt::class);
    }
}
