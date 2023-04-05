<?php declare(strict_types=1);

namespace App\Enums;

enum ChallengeStatusEnum: int
{
    /**
     * const.php と定義を合わせること
     */
    case Start = 0;   // 挑戦中
    case Lating = 1; // 達成はしたが達成期限を過ぎている
    case Retry = 2;   // 再挑戦
    case Done = 10;   // 達成
    case Lated = 20;  // 達成はしたが達成期限を過ぎている
    case Failed = 30;  // 失敗

    /**
     * status値にマッチしたステータス名を返却
     *
     * @param integer $status
     * @return string ステータス名
     */
    public static function string(int $role): string
    {
        return match ($role) {
            self::Start, self::Start->value => '挑戦中',
            self::Lating, self::Lating->value => '挑戦中(期限過ぎ)',
            self::Retry, self::Retry->value => '再達成',
            self::Done, self::Done->value => '達成',
            self::Lated, self::Lated->value => '達成(期限過ぎ',
            self::Failed, self::Failed->value => '失敗',
        };
    }
}
