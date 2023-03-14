[<script setup lang="ts">
import { computed, inject } from 'vue'
import { useUserStore,useRequestStore } from '../store/globalStore'
import { router } from '../routes/routes'
import { Repositories } from '../apis/repositoryFactory'

// utility
const requestStore = useRequestStore()
const userStore = useUserStore()
const $repositories = inject<Repositories>('$repositories')!

const logout = () => {
    requestStore.setLoading(true)
    // axiosでログイン情報を削除
    $repositories.auth.logout()
        .then(response => {
            if (response.status === 204) {
                // vuexのログイン状態も解除
                userStore.setLogin(false)
                // TOP画面へ遷移
                setTimeout(() => {
                    router.push({ name: 'login' })
                }, 2000)
            }
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}

/**
 * ページのモードに合わせたTOPページ名
 */
const topPageName = computed(() => {
    return 'login'
})
/**
 * ページがユーザーかどうか
 */
const userMode = computed(() => {
    return true
})

const isLogin = computed(() => {
    return userStore.isLogin
})

const userImage = computed(() => {
    return userStore.user.image_url ?? ''
})
</script>

<template>
    <div>
        <header class="c-header c-header--user">
            <router-link :to="{ name: topPageName }">
                <div class="c-logo__container">
                    <span class="c-logo">Step</span>
                </div>
            </router-link>
            <!-- PC用メニュー -->
            <nav class="c-nav">
                <ul class="c-nav__list">
                    <!-- ログイン済メニュー -->
                    <template v-if="isLogin">
                        <!-- ユーザーメニュー -->
                        <router-link :to="{ name: 'steps-create' }">
                            <li class="c-nav__list-item">
                                <span class="c-nav__list-link">ステップを投稿する</span>
                            </li>
                        </router-link>
                        <li @click="logout" class="c-nav__list-item">
                            <span class="c-nav__list-link">ログアウト</span>
                        </li>
                        <router-link :to="{ name: 'todo' }">
                            <li class="c-nav__list-item">
                                <div class="c-img--user-icon--small">
                                    <img
                                        :src="userImage"
                                        alt="ログインユーザー画像"
                                    />
                                </div>
                            </li>
                        </router-link>
                        <router-link :to="{ name: 'todo' }">
                            <li class="c-nav__list-item">
                                <cart-icon />
                            </li>
                        </router-link>
                    </template>
                    <!-- ゲストメニュー -->
                    <template v-else>
                        <!-- ユーザーメニュー -->
                        <router-link :to="{ name: 'steps-list' }">
                            <li class="c-nav__list-item">
                                <a href="#" class="c-nav__list-link">ステップを見る</a>
                            </li>
                        </router-link>
                        <router-link :to="{ name: 'register' }">
                            <li class="c-nav__list-item">
                                <a href="#" class="c-nav__list-link">アカウント登録</a>
                            </li>
                        </router-link>
                        <router-link :to="{ name: 'login' }">
                            <li class="c-nav__list-item">
                                <a href="#" class="c-nav__list-link">ログイン</a>
                            </li>
                        </router-link>
                    </template>
                </ul>
            </nav>
        </header>
    </div>
</template>

<style lang="scss" scoped>
// ロゴ文字だけをリクエスト
// https://developers.google.com/fonts/docs/css2?hl=ja#optimizing_your_font_requests
@import url('https://fonts.googleapis.com/css2?family=Montserrat+Subrayada&display=swap&text=Step');
.c-logo {
    font-family: 'Montserrat Subrayada', sans-serif;
    letter-spacing: normal;
}
</style>
