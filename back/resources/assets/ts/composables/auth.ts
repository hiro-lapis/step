import { inject } from 'vue'
import { useUserStore,useRequestStore } from '../store/globalStore'
import { Repositories } from '../apis/repositoryFactory'
import { router } from '../routes/routes'
import { repositoryKey } from '../types/common/Injection'

export const useAuthFunc = () => {
    const requestStore = useRequestStore()
    const userStore = useUserStore()
    const $repositories = inject<Repositories>(repositoryKey)!

    /**
     * ログアウトAPI実行し、引数で指定したページへ遷移
     *
     * @param next ログアウト後に遷移するページ
     */
    const logout = (next: string = 'home') => {
        requestStore.setLoading(true)
        // axiosでログイン情報を削除
        $repositories.auth.logout()
            .then(response => {
                // vuexのログイン状態も解除
                userStore.setLogin(false)
                // 今いるページがログイン必須のページだったらTOPへ
                if (router.currentRoute.value.meta.requireAuth) {
                    // TOP画面へ遷移
                    setTimeout(() => {
                        router.push({ name: next })
                    }, 2000)
                }
            })
            .finally(() => {
                requestStore.setLoading(false)
            })
    }
    return { logout }
}
