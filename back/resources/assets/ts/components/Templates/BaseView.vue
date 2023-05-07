
<script setup lang="ts">
import { inject, onMounted } from 'vue'
import LoadingIcon from '../Atoms/LoadingIcon.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { repositoryKey } from '../../types/common/Injection'

// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>(repositoryKey)!
// props
defineProps({
    className: { required: false, type: String, default: 'p-container', },
})
// methods
onMounted(() => {
    // ログイン状態チェック
    const isGuestOnlyPage = router.currentRoute.value.meta.guestOnly
    const isGuestPage = !router.currentRoute.value.meta.requiresAuth
    $repositories.auth.isLogin().then(response => {
        if (response.data.is_login) {
            userStore.setLogin(true)
            userStore.setUser(response.data.user!)
            userStore.setChallengingStepIds(response.data.step_ids)
            userStore.setRemainCount(response.data.remain_count)
            // ログイン中で未ログイン限定ページを表示中ならログイン後のTOP画面へ
            if (isGuestOnlyPage) {
                router.push({ name: 'steps-list' })
            }
        } else {
            userStore.setLogin(false)
            userStore.initUser()
            userStore.initRemainCount()
            // ログイン状態のページを表示中 state更新しログイン画面へ
            if (!isGuestPage) {
                router.push({ name:'login' })
            }
        }
    })
})
</script>

<template>
    <LoadingIcon />
    <NotifyMessage />
    <Header />
        <div class="l-container" :class="className">
            <slot name="content">
            </slot>
        </div>
    <Footer />
</template>

<style scoped lang="scss">
</style>
