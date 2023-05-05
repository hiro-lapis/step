<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    |
    | @see library:https://github.com/openai-php/laravel
    | @see api:https://platform.openai.com/docs/api-reference/completions
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    // 利用するAIモデル
    'models' => 'text-davinci-003',
    // プロンプト設定値
    'params' => [
        // 入出力を合わせた最大トークン数
        // 1000tokens => $0.02(0.27円)
        'max_tokens' => 1000,
        // 生成する文章の精度(low(確定的) 0.0 ~ 2.0 high(ランダム性高))
        // 'temperature' => 0.7,
    ],

];
