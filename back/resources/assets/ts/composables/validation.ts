import { computed } from 'vue'

/**
 * バリデーション
 *
 * @returns true | string (エラーメッセージ)
 */
export const useValidation = () => {
    // 入力必須(空文字, null, undefined)
    const required = (value: string) => {
        return !!value || '入力必須です'
    }
    // 入力必須(空文字, null, undefined)
    const selectRequired = (value: number) => {
        return !!value || '入力必須です'
    }
    // 最大文字数
    const max = (value: string, max: number) => {
        return (value || '').length <= max || `${max}文字以内で入力してください`
    }
    // 最小文字数
    const min = (value: string, min: number) => {
        return (value || '').length >= min || `${min}文字以上で入力してください`
    }
    // 最大値
    const maxValue = (value: number, max: number) => {
        return value <= max || `${max}以下で入力してください`
    }
    // 最小値
    const minValue = (value: number, min: number) => {
        return value >= min || `${min}以上で入力してください`
    }
    // メールアドレス
    const email = (value: string) => {
        return (
            (value || '').match(/^[^@]+@[^@]+\.[^@]+$/) ||
            'メールアドレスを入力してください'
        )
    }
    // パスワード
    const password = (value: string) => {
        return (
            // (?=.*?\d) は数字を1文字以上含む
            (value || '').match(/^(?=.*?[a-z])[a-z\d]{8,24}$/i) !== null ||
            // (value || '').match(/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,24}$/i) !== null  ||
            '半角英字で8~24文字以内で入力してください'
        )
    }
    // パスワード（確認）
    const passwordConfirmation = (value: string, password: string) => {
        return (value || '') === password || 'パスワードが一致しません'
    }
    // 電話番号
    const tel = (value: string) => {
        return (
            (value || '').match(/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/) ||
            '電話番号を入力してください'
        )
    }
    // 郵便番号
    const postalCode = (value: string) => {
        return (
            (value || '').match(/^[0-9]{3}-[0-9]{4}$/) ||
            '郵便番号を入力してください'
        )
    }
    // ひらがな
    const hiragana = (value: string) => {
        return (
            (value || '').match(/^[\u3040-\u309F]+$/) ||
            'ひらがなで入力してください'
        )
    }
    // カタカナ
    const katakana = (value: string) => {
        return (
            (value || '').match(/^[\u30A0-\u30FF]+$/) ||
            'カタカナで入力してください'
        )
    }
    // 半角英数字
    const alphaNum = (value: string) => {
        return (
            (value || '').match(/^[a-z\d]+$/i) ||
            '半角英数字で入力してください'
        )
    }
    // 半角英字
    const alpha = (value: string) => {
        return (
            (value || '').match(/^[a-z]+$/i) ||
            '半角英字で入力してください'
        )
    }
    // 半角数字
    const numeric = (value: string) => {
        return (
            (value || '').match(/^[0-9]+$/) ||
            '半角数字で入力してください'
        )
    }
    // 半角記号
    const symbol = (value: string) => {
        return (
            (value || '').match(/^[!-/:-@[-`{-~]+$/) ||
            '半角記号で入力してください'
        )
    }
    // 半角スペース
    const space = (value: string) => {
        return (
            (value || '').match(/^[^ ]+$/) ||
            '半角スペースは使用できません'
        )
    }
    // URL
    const url = (value: string) => {
        return (
            (value || '').match(
                /^(https?|chrome):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+$/
            ) || 'URLを入力してください'
        )
    }
    // 日付
    const date = (value: string) => {
        return (
            (value || '').match(
                /^\d{4}-\d{2}-\d{2}$/
            ) || '日付を入力してください'
        )
    }
    // 時間
    const time = (value: string) => {
        return (
            (value || '').match(
                /^\d{2}:\d{2}$/
            ) || '時間を入力してください'
        )
    }
    // 日付時間
    const datetime = (value: string) => {
        return (
            (value || '').match(
                /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/
            ) || '日付時間を入力してください'
        )
    }
    // 日付範囲
    const dateRange = (value: string, min: string, max: string) => {
        return (
            (value || '').match(
                /^\d{4}-\d{2}-\d{2}$/
            ) || '日付を入力してください'
        )
    }
    // 時間範囲
    const timeRange = (value: string, min: string, max: string) => {
        return (
            (value || '').match(
                /^\d{2}:\d{2}$/
            ) || '時間を入力してください'
        )
    }

    /**
     * 各種項目のバリデーション
     */
    const stepName = (name: string) => {
        let result = required(name)
        if (result !== true) {
            return result
        }
        result = max(name, 255)
        return result
    }
    const stepSummary = (summary: string) => {
        return max(summary, 500)
    }
    const stepTimeCount = (timeCount: number, achievementTimeType: number) => {
        let result = selectRequired(timeCount)
        if (result === true) {
            return result
        }
        result = minValue(timeCount, 1)
        if (result === true) {
            return result
        }
        switch (timeCount) {
            case 1:
                return maxValue(timeCount, 59)
            case 2:
                return maxValue(timeCount, 23)
            case 3:
                return maxValue(timeCount, 30)
            case 4:
                return maxValue(timeCount, 6)
            case 5:
                return maxValue(timeCount, 11)
            case 6:
                return maxValue(timeCount, 99)
            default:
                return true
        }
    }

    return {
        stepName,
        stepSummary,
        stepTimeCount,
        required,
        selectRequired,
        max,
        min,
        maxValue,
        minValue,
        email,
        password,
        passwordConfirmation,
        tel,
        postalCode,
        hiragana,
        katakana,
        alphaNum,
        alpha,
        numeric,
        symbol,
        space,
        url,
        date,
        time,
        datetime,
        dateRange,
        timeRange,
    }
}
