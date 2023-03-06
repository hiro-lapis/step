<script setup lang="ts">
import FadeIn from './Atoms/Transition/FadeIn.vue'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import axios from 'axios'
import { router } from '../../../assets/ts/routes/routes'
import { useUserStore } from '../../../assets/ts/store/globalStore'

// utility
const userStore = useUserStore()
// data
const windowSize = ref(0)
const isActive = ref(false)
const nextRoute = ref('')

// methods

const onResize = () => {
    windowSize.value = window.innerWidth
}
const logout = async(next: string) => {
    // ローディングONtNextPage(
    axios
        .post("/api/logout")
        .then(response => {
            // ログイン状態解除
            userStore.setLogin(false)

            setTimeout(() => {
                router.push({ name: next });
            }, 2000);
        })
        .finally(() => {
            // ローディングOFF
        })
}
const setNextPage = (next) => {
    // ログアウト後に表示するページ設定をしてログアウト
    nextRoute.value = next;
}
const isGuest = computed(() => userStore.isLogin)
const pcSize = computed(() => windowSize.value > 400)

onMounted(() => {
    onResize()
    window.removeEventListener("resize", onResize)
})
onBeforeUnmount(() => {
    onResize()
})
</script>


<template>
    <footer class="l-footer">
        <!-- pc footer -->
        <template v-if="pcSize">
            <div class="c-footer u-color--user">
                <div class="c-footer__head">
                    <router-link :to="{ name: 'todo' }">
                        <div class="c-logo__container--footer">
                            TODO:ロゴ
                            <!-- <img src="https://laravel8-haiki-share.s3-ap-northeast-1.amazonaws.com/asset/logo.png" class="c-logo"/> -->
                        </div>
                    </router-link>
                </div>
                <div class="c-footer__body">
                    <section class="c-footer__link">
                        <p class="c-footer__link-title">サービスについて</p>
                        <ul class="c-footer__link__container">
                            <li class="c-footer__link__list">
                                <span class="c-text-link" href="">はじめての方へ</span>
                            </li>
                            <li class="c-footer__link__list">
                                <span class="c-text-link" href="">使い方</span></li>
                            <li class="c-footer__link__list">
                                <span class="c-text-link" href="">利用規約</span>
                            </li>
                            <li class="c-footer__link__list">
                                <span class="c-text-link" href="">個人情報保方針</span>
                            </li>
                        </ul>
                    </section>
                    <section class="c-footer__link">
                        <p class="c-footer__link-title">メニュー</p>
                        <ul class="c-footer__link__container">
                            <template>
                                <router-link :to="{ name:'todo' }" exact>
                                    <li class="c-footer__link__list">
                                        <span class="c-text-link" href="">トップページ</span>
                                    </li>
                                </router-link>
                                <template v-if="isGuest">
                                    <router-link :to="{ name:'todo' }">
                                        <li class="c-footer__link__list">
                                            <span class="c-text-link">ログイン</span>
                                        </li>
                                    </router-link>
                                    <router-link :to="{ name:'todo' }">
                                        <li class="c-footer__link__list">
                                            <span class="c-text-link">アカウント登録</span>
                                        </li>
                                    </router-link>
                                </template>
                                <router-link :to="{ name:'todo' }">
                                    <li class="c-footer__link__list">
                                        <span class="c-text-link">商品をみる</span>
                                    </li>
                                </router-link>
                            </template>
                        </ul>
                    </section>
                    <section class="c-footer__link">
                        <p class="c-footer__link-title">サポート</p>
                        <ul class="c-footer__link__container">
                            <li class="c-footer__link__list">
                                <span class="c-text-link">よくある質問</span>
                            </li>
                            <li class="c-footer__link__list">
                                <span class="c-text-link">お問い合わせ</span>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </template>
        <!-- SPフッター -->
        <template v-else>
            <!-- ユーザーナビゲーション -->
            <div class="c-sp-footer">
                <template>
                    <ul class="c-sp-footer-nav__container">
                        <li
                            @click="isActive = !isActive"
                            class="c-sp-footer-nav__item"
                        >
                            <i class="fas fa-bars"></i>
                            <span class="c-sp-footer-nav__item-text">メニュー</span>
                        </li>
                        <router-link
                            @click.native="isActive = false"
                            :to="{ name: 'todo'}"
                        >
                            <li class="c-sp-footer-nav__item">
                                <i class="fas fa-door-open"></i>
                                <span class="c-sp-footer-nav__item-tex">ログイン</span>
                            </li>
                        </router-link>
                        <li class="c-sp-footer-nav__item">
                            <i class="fas fa-search"></i>
                            <span class="c-sp-footer-nav__item-text">検索</span>
                        </li>
                        <li class="c-sp-footer-nav__item">
                            <i class="fas fa-book"></i>
                            <span class="c-sp-footer-nav__item-text">ガイド</span>
                        </li>
                    </ul>
                </template>
            </div>
        </template>
        <!-- SPメニュー -->
        <fade-in>
            <div
                v-if="isActive"
                @click="isActive = false"
                class="c-sp-nav__bg"
            ></div>
        </fade-in>
        <nav class="c-sp-nav" :class="{ active: isActive }">
            <ul class="c-sp-nav__list">
                <router-link
                    @click.native="isActive = false"
                    :to="{ name: 'todo' }"
                >
                    <li class="c-sp-nav__list-item">
                        <i class="c-sp-nav__list-icon fas fa-chevron-right"></i
                        >アイデアを探す
                    </li>
                </router-link>
                <router-link
                    @click.native="isActive = false"
                    :to="{ name: 'todo' }"
                >
                    <li class="c-sp-nav__list-item">
                        <i class="c-sp-nav__list-icon fas fa-chevron-right"></i
                        >アイデアを登録
                    </li>
                </router-link>
                <!-- ログイン時のみ表示 -->
                <template v-if="!isGuest">
                    <router-link
                        @click.native="isActive = false"
                        :to="{ name: 'todo' }"
                    >
                        <li class="c-sp-nav__list-item">
                            <i class="c-sp-nav__list-icon fas fa-chevron-right"></i>マイページ
                        </li>
                    </router-link>
                    <li @click="logout('todo')" class="c-sp-nav__list-item">
                        <i class="c-sp-nav__list-icon fas fa-chevron-right"></i>ログアウト
                    </li>
                </template>
            </ul>
        </nav>
    </footer>
</template>
