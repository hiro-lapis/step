<script setup lang="ts">
import axios from 'axios'
import { onMounted } from 'vue'
import Loading from '../Atoms/Loading.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'

// utilities
const router = useRouter()
const userStore = useUserStore()

onMounted(() => {
    // ログイン状態チェック
    axios.get('/api/is-login')
        .then(response => {
            if (response.data.is_login) {
                userStore.setLogin(true)
                userStore.setUser(response.data.user)
                // TODO:if-未ログイン状態のページを表示中
                // ログイン後のTOP画面へ

                router.push({ name: 'themes-list' })
            } else {
                // TODO:if-ログイン状態のページを表示中
                // state更新しログイン画面へ
                userStore.setLogin(false)
                userStore.initUser()
                router.push('login')
            }
        })
})
</script>

<template>
    <Loading />
    <NotifyMessage />
    <Header />
        <slot name="content">
        </slot>
    <Footer />
</template>
