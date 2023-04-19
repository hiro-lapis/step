<?php
// レスポンスのメッセージを「コントローラ + s(正常)/f(エラー) + メッセージ内容)」で登録
return [
    'order_description' => ':idea_name の購入決済',
    'will_move_for_payment' => '購入手続きを開始しました。数分後にメール届くメールから決済を進めてください',
    'create_payment_has_failed' => 'Paypayとの連携処理に失敗しました。<br>10分ほど時間をおいて再度お試しください',
    'error_has_occured' => 'Paypayとの連携処理に失敗しました。<br>10分ほど時間をおいて再度お試しください',
    'duplicate_dynamic_qr_request' => '先ほど作成したQRコードから決済を進めてください',
];
