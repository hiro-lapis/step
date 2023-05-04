/**
 * バリデーションルール
 * 入力値を受け取り、OKなときはtrue
 * エラー時はメッセージ文字列を返す
 */
export type ValidationRules = (params:any) => true|String
