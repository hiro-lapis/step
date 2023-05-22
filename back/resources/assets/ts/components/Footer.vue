<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useUserStore } from '../store/globalStore'

// utility
const userStore = useUserStore()
// props
defineProps({
    isFix: { required: false, type: Boolean, default: false}, // 下部固定フラグ
})
// data
const windowSize = ref(0)
// methods
const onResize = () => {
    windowSize.value = window.innerWidth
}
// computed
const isLogin = computed(() => userStore.isLogin)
const logoRoute = computed(() => isLogin.value ? { name: 'steps-list' } : { name: 'home' })
onMounted(() => {
    onResize()
    window.removeEventListener('resize', onResize)
})
onBeforeUnmount(() => {
    onResize()
})
</script>


<template>
    <footer class="l-footer" :style="{ position: isFix ? 'fixed' : 'relative' }">
        <!-- pc footer -->
        <div class="c-footer u-color--theme">
            <div class="c-footer__head">
                <router-link :to="logoRoute">
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
