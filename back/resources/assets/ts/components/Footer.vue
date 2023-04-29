<script setup lang="ts">
import FadeIn from './Atoms/Transition/FadeIn.vue'
import { computed, onBeforeUnmount, onMounted, ref, StyleValue } from 'vue'
import axios from 'axios'
import { router } from '../routes/routes'
import { useUserStore } from '../store/globalStore'

// utility
const userStore = useUserStore()

// props
defineProps({
    isFix: { required: true, type: Boolean, default: false}, // 下部固定フラグ
})
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
            }, 2000)
        })
        .finally(() => {
            // ローディングOFF
        })
}
const setNextPage = (next) => {
    // ログアウト後に表示するページ設定をしてログアウト
    nextRoute.value = next;
}
const isLogin = computed(() => userStore.isLogin)
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
    <footer class="l-footer" :style="{ position: isFix ? 'fixed' : 'relative' }">
        <!-- pc footer -->
        <div class="c-footer u-color--user">
            <div class="c-footer__head">
                <router-link :to="{ name: 'todo' }">
                    <div class="c-logo__container--footer">
                        <img src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/logos/logo-full.png" class="c-logo"/>
                    </div>
                </router-link>
            </div>
            <div class="c-footer__body">
                <section class="c-footer__link">
                    <p class="c-footer__link-title">サービス</p>
                    <ul class="c-footer__link__container">
                        <li class="c-footer__link__list">
                            <router-link class="c-text-link--footer" :to="{ name: 'steps-list' }">
                                ステップ一覧
                            </router-link>
                        </li>
                        <li class="c-footer__link__list">
                            <router-link v-if="isLogin" class="c-text-link--footer" :to="{ name: 'steps-create' }">
                                ステップ登録
                            </router-link>
                        </li>
                        <li class="c-footer__link__list">
                            <router-link v-if="!isLogin" class="c-text-link--footer" :to="{ name: 'register' }">
                                会員登録
                            </router-link>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </footer>
</template>
