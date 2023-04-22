<?php declare(strict_types=1);

namespace App\Enums;

enum ChallengeStatusEnum: int
{
    case NotChallenged = 0; // 未挑戦
    case Challenging = 1; // 挑戦中
    case Retry = 2;   // 再挑戦
    case Lating = 3; // 挑戦中で達成期限を過ぎている
    case Cleared = 10;   // 達成
    case Lated = 20;  // 達成はしたが達成期限を過ぎていた
    case Failed = 30;  // 失敗

    /**
     * status値にマッチしたステータス名を返却
     *
     * @param integer $status
     * @return string ステータス名
     */
    public static function string(int $status): string
    {
        return match ($status) {
            self::NotChallenged, self::NotChallenged->value => '未',
            self::Challenging, self::Challenging->value => '挑戦中',
            self::Lating, self::Lating->value => '挑戦中(期限過ぎ)',
            self::Retry, self::Retry->value => '再挑戦',
            self::Cleared, self::Cleared->value => '達成',
            self::Lated, self::Lated->value => '達成(期限過ぎ',
            self::Failed, self::Failed->value => '失敗',
        };
    }

    public static function getInChallengeStatuses(): array
    {
        return [
            self::Challenging->value,
            self::Lating->value,
            self::Retry->value,
        ];
    }

    /**
     * 達成済みのステータスを返却
     *
     * @return array
     */
    public static function getClearedStatuses(): array
    {
        return [
            self::Cleared,
            self::Lated,
        ];
    }

    /**
     * チャレンジ状態かどうか判定
     *
     * @param integer $status
     * @return boolean
     */
    public static function isInChallenge(int $status): bool
    {
        return match ($status) {
            self::NotChallenged, self::NotChallenged->value => false,
            self::Challenging, self::Challenging->value => true,
            self::Lating, self::Lating->value => true,
            self::Retry, self::Retry->value => true,
            self::Cleared, self::Cleared->value => false,
            self::Lated, self::Lated->value => false,
            self::Failed, self::Failed->value => false,
        };
    }
}
