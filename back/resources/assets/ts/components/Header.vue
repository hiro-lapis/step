<script setup lang="ts">
import { computed, inject, provide, Ref } from 'vue'
import { useRequestStore, useUserStore } from '../store/globalStore'
import HumbargarNav from '../components/Atoms/HumbargarNav.vue'
import { useAuthFunc } from '../composables/auth'
import { useRoute } from 'vue-router'
import KeyWordInput from './Atoms/KeyWordInput.vue'
import { Condition } from '../types/components/Condition'
import { conditionKey } from '../types/common/Injection'
import { useStepListStore } from '../store/stepListStore'

// utility
const userStore = useUserStore()
const route = useRoute()
const requestStore = useRequestStore()
const { fetchData } = useStepListStore()
// data

// StepList.vueと共有する検索条件
const condition = inject<Ref<Condition>>(conditionKey)!

// provide

// methods
const { logout } = useAuthFunc()
// computed
const showSearchUi = computed(() => {
    return route.name === 'steps-list'
})
// ログイン状態で遷移先変更
const topPageName = computed(() => userStore.isLogin ? 'steps-list' : 'home')
const isLogin = computed(() => userStore.isLogin)
const userImage = computed(() => userStore.user.image_url ?? '')
const search: () => Promise<void> = async () => {
    // ページ番号を初期化
    condition.value.page = 1
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await fetchData(condition.value)
    requestStore.setLoading(false)
}
provide<() => Promise<void>>('search', search)
</script>

<template>
    <div>
        <header class="c-header c-header--user">
            <router-link :to="{ name: topPageName }">
                <div class="c-logo--header">
                    <img src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/logos/logo-full.png" class="c-logo"/>
                </div>
            </router-link>
            <!-- PC用メニュー -->
            <nav class="c-nav">
                <ul class="c-nav__list">
                    <div v-if="showSearchUi" class="c-nav__list__keyword-input">
                        <KeyWordInput
                            :placeHolder="''"
                            @keyupEnter="search()"
                            v-model:value="condition.key_word"
                        />
                    </div>
                    <!-- ログイン済メニュー -->
                    <template v-if="isLogin">
                        <!-- ユーザーメニュー -->
                        <router-link :to="{ name: 'steps-create' }">
                            <li class="c-nav__list-item">
                                <span class="c-nav__list-link">ステップを投稿する</span>
                            </li>
                        </router-link>
                        <!-- ステップ一覧 -->
                        <router-link :to="{ name: 'steps-list' }">
                            <li class="c-nav__list-item">
                                <a href="#" class="c-nav__list-link">ステップ一覧</a>
                            </li>
                        </router-link>
                        <li @click="logout()" class="c-nav__list-item">
                            <span class="c-nav__list-link">ログアウト</span>
                        </li>
                        <router-link :to="{ name: 'mypage' }">
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
                    <!-- ゲストメニュー -->
                    <template v-else>
                        <!-- ユーザーメニュー -->
                        <router-link :to="{ name: 'steps-list' }">
                            <li class="c-nav__list-item">
                                <a href="#" class="c-nav__list-link">ステップ一覧</a>
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
            <!-- スマホ用メニュー -->
            <div class="c-sp-menu">
                <HumbargarNav />
            </div>
        </header>
    </div>
</template>

<style lang="scss" scoped>
// ロゴ文字だけをリクエスト
// https://developers.google.com/fonts/docs/css2?hl=ja#optimizing_your_font_requests
@import url('https://fonts.googleapis.com/css2?family=Montserrat+Subrayada&display=swap&text=Step');
@import "../../sass/foundation/_breakpoints.scss";

.c-logo {
    font-family: 'Montserrat Subrayada', sans-serif;
    letter-spacing: normal;
}

.c-sp-menu {
    display: inline-block;
    @include mq() {
        display: none;
    }
}
</style>
