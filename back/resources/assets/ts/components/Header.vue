[<script setup lang="ts">
import { computed } from 'vue'
import axios from 'axios'
import SlideDownMessage from '../components/Atoms/Transition/SlideDown.vue'
import { useUserStore,useRequestStore } from '../store/globalStore'
import { router } from '../routes/routes'

const requestStore = useRequestStore()
const userStore = useUserStore()

const logout = () => {
    requestStore.setLoading(true)
    // axiosでログイン情報を削除
    axios
        .post('/api/logout')
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
        .catch(error => {
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
        <header :class="[userMode ? 'c-header--user': 'c-header--shop']" class="c-header">
            <router-link :to="{ name: topPageName }">
                <div class="c-logo__container">
                    <img src="https://laravel8-haiki-share.s3-ap-northeast-1.amazonaws.com/asset/logo.png" class="c-logo"/>
                </div>
            </router-link>
            <!-- PC用メニュー -->
            <nav class="c-nav">
                <ul class="c-nav__list">
                    <!-- ログイン済メニュー -->
                    <template v-if="isLogin">
                        <!-- ユーザーメニュー -->
                        <template v-if="userMode">
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <span class="c-nav__list-link">商品を見る</span>
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
                        <!-- ショップメニュー -->
                        <template v-else>
                            <router-link :to="{ name: 'todo' }" exact>
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link"
                                        >商品を登録</a
                                    >
                                </li>
                            </router-link>
                            <router-link :to="{ name: 'todo' }" exact>
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link"
                                        >商品一覧</a
                                    >
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
                        </template>
                    </template>
                    <!-- ゲストメニュー -->
                    <template v-else>
                        <!-- ユーザーメニュー -->
                        <template v-if="userMode">
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link">商品を見る</a>
                                </li>
                            </router-link>
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link">アカウント登録</a>
                                </li>
                            </router-link>
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link">ログイン</a>
                                </li>
                            </router-link>
                        </template>
                        <!-- ショップメニュー-->
                        <template v-else>
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link">アカウント登録</a>
                                </li>
                            </router-link>
                            <router-link :to="{ name: 'todo' }">
                                <li class="c-nav__list-item">
                                    <a href="#" class="c-nav__list-link">ログイン</a>
                                </li>
                            </router-link>
                        </template>
                    </template>
                </ul>
            </nav>
        </header>
    </div>
</template>
