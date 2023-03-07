<script setup lang="ts">
import { inject, onMounted } from 'vue'
import Loading from '../Atoms/Loading.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { guestPageUrl } from '../../routes/routes'

// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>("$repositories")!

onMounted(() => {
    // ログイン状態チェック
    const isGuestPage = guestPageUrl.includes(router.currentRoute.value.path)
    $repositories.auth.isLogin().then(response => {
        if (response.data.is_login) {
            userStore.setLogin(true)
            userStore.setUser(response.data.user)
            // 未ログイン用ページを表示中ならログイン後のTOP画面へ
            if (isGuestPage) {
                router.push({ name: 'themes-list' })
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
        <div class="l-container p-container">
            <slot name="content">
            </slot>
        </div>
    <Footer />
</template>
