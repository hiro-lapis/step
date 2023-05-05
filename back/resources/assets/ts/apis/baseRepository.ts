/**
 * repository patternによるフロントエンドAPI実装
 * base.repository => 共通設定
 * xxxx.repository => 各種API
 * repository.factory => リポジトリをまとめ、引数で指定されたリポジトリを返却
 * [注意]
 * piniaの初期化前にrepositoriesの設定がされるため、storeを使おうとするとエラーが出る
 * store処理は呼び出し元で行う
 */

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the 'XSRF' token cookie.
 */

import axios, { AxiosError } from 'axios'
import { useMessageInfoStore } from '../store/globalStore';

// グローバルオブジェクトへの登録
declare global {
    interface Window {
      axios: any;
    }
}

// フロントエンドをS3から配信する場合はBASE URL設定
// const baseURL = 'https://jsonplaceholder.typicode.com/'
// const $axios = axios.create({
//   baseURL: baseURL,
// })

// Provide / Injectする場合は変更すること
window.axios = axios
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// SPA認証のためのcredentialを設定
axios.defaults.withCredentials = true

// リクエスト実行前の前処理
axios.interceptors.request.use((req): any => {
    return req;
})


// リクエスト実行後の後処理
axios.interceptors.response.use(
    (res): any => {
        return res;
    },
    // 構造例: errors: [name: ['入力必須です', '文字数が多すぎます']]]
    (error: AxiosError<{ message?: string, errors?: Array<Record<string, Record<string, string>>>}>) => {
        // pinia初期化後のinterceptor内でstore使用
        const msg = useMessageInfoStore()
        let messages = ''

        // errorsという配列があればメッセージに追加
        if (error.response!.data.errors && typeof error.response!.data.errors === 'object') {
            const keys = Object.keys(error.response!.data.errors)
            // 各項目の配列形式で入っているバリデーションメッセージを改行区切りでセット
            keys.forEach((key) => {
                    error.response!.data.errors![key].forEach(e => {
                        messages += e + '\n'
                    })
            })
        } else if (error.response!.data && error.response!.data.message!) {
            messages += error.response!.data.message + '\n'
        }
        // reponse.data.messageがあればそれを表示メッセージに追加
        // サーバー側からメッセージがない場合は、エラーのステータスコードによってメッセージをセット
        if (messages === '') {
            const res = error.response!
            switch (res.status) {
                case 401:
                    messages = '認証に失敗しました。'
                    break
                case 403:
                    messages = 'アクセス権限がありません。'
                    break
                case 404:
                    messages = 'データが見つかりませんでした。'
                    break
                case 405:
                    messages = '許可されていないメソッドです。'
                    break
                case 500:
                    messages = 'サーバー側でエラーが発生しました。'
                    break
                case 503:
                    messages = 'サーバーがメンテナンス中です。'
                    break
            }
        }
        // 一連のエラー情報を表示
        msg.setErrorMessage(messages)
      return Promise.reject(error);
    }
)

export default axios
