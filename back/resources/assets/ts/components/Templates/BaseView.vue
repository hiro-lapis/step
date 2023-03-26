
<script setup lang="ts">
import { inject, onMounted } from 'vue'
import Loading from '../Atoms/Loading.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { guestPageName, guestOnlyPageName } from '../../routes/routes'

// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>("$repositories")!

// props
defineProps({
    className: { required: false, type: String, default: 'p-container', },
})
onMounted(() => {
    // ログイン状態チェック
    const isGuestOnlyPage = guestOnlyPageName.includes(router.currentRoute.value.name!.toString())
    const isGuestPage = guestPageName.includes(router.currentRoute.value.name!.toString())
    $repositories.auth.isLogin().then(response => {
        if (response.data.is_login) {
            userStore.setLogin(true)
            userStore.setUser(response.data.user)
            // ログイン中で未ログイン限定ページを表示中ならログイン後のTOP画面へ
            if (isGuestOnlyPage) {
                router.push({ name: 'steps-list' })
            }
        } else {
            userStore.setLogin(false)
            userStore.initUser()
            // ログイン状態のページを表示中 state更新しログイン画面へ
            if (!isGuestPage) {
                router.push({ name:'login' })
            }
        }
    })
})
</script>

<template>
    <Loading />
    <NotifyMessage />
    <Header />
        <div class="l-container" :class="className">
            <slot name="content">
            </slot>
        </div>
    <Footer />
</template>

<style scoped lang="scss">

// 各ページでコンテンツを表示するwidthスタイルを設定
// 適宜propsで変更する
.p-container {
    max-width: 1440px;
    &__steps-list {
        max-width: 1000px;
    }
    &--steps-form {
        max-width: 1440px;
        margin-bottom: 250px;
    }
    &__mypage {
        max-width: 1440px;
        margin-bottom: 300px;
    }
}

</style>
