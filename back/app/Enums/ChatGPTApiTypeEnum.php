<?php declare(strict_types=1);

namespace App\Enums;

/**
 * chat GPT APIの種類
 */
enum ChatGPTApiTypeEnum: int
{
    case Completion = 1; // 入力補完
}
