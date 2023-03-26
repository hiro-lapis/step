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

import axios from 'axios'
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
    console.log('req', req)
    return req;
})


// リクエスト実行後の後処理
axios.interceptors.response.use(
    (res): any => {
        console.log('res', res)
        return res;
    },
    (error) => {
        // pinia初期化後のinterceptor内でstore使用
        const msg = useMessageInfoStore()
        switch (error.response.status) {
            case 422:
                if (error.response.data.errors && typeof error.response.data.errors === 'object') {
                    const keys = Object.keys(error.response.data.errors)
                    let messages = ''
                    // 各項目の配列形式で入っているバリデーションメッセージを改行区切りでセット
                    keys.forEach((key) => {
                            error.response.data.errors[key].forEach(e => {
                                messages += e + '\n'
                            })
                    })
                    msg.setErrorMessage(messages)
                }
                break
        }
      return Promise.reject(error);
    }
)

export default axios
