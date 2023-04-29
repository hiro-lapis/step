import { inject } from 'vue'
import { useUserStore,useRequestStore } from '../store/globalStore'
import { Repositories } from '../apis/repositoryFactory'
import { router } from '../routes/routes'

export const useAuthFunc = () => {
    const requestStore = useRequestStore()
    const userStore = useUserStore()
    const $repositories = inject<Repositories>('$repositories')!

    /**
     * ログアウトAPI実行し、引数で指定したページへ遷移
     *
     * @param next ログアウト後に遷移するページ
     */
    const logout = (next: string = 'login') => {
        requestStore.setLoading(true)
        // axiosでログイン情報を削除
        $repositories.auth.logout()
            .then(response => {
                if (response.status === 204) {
                    // vuexのログイン状態も解除
                    userStore.setLogin(false)
                    // 今いるページがログイン必須のページだったらTOPへ
                    if (router.currentRoute.value.meta.requireAuth) {
                        // TOP画面へ遷移
                        setTimeout(() => {
                            router.push({ name: next })
                        }, 2000)
                    }
                }
            })
            .finally(() => {
                requestStore.setLoading(false)
            })
    }
    return { logout }
}
