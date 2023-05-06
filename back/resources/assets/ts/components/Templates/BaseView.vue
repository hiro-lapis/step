
<script setup lang="ts">
import { inject, InjectionKey, onMounted, provide, ref, Ref } from 'vue'
import LoadingIcon from '../Atoms/LoadingIcon.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { Condition }  from '../../types/components/Condition'
import { conditionKey, repositoryKey } from '../../types/common/Injection'

/**
 * SPA ページのベースとなるコンポーネント
 * axios,store,router設定を除いた共通処理(provide,inject)はここに書く
 */
// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>(repositoryKey)!
// const $repositories = inject<Repositories>(repositoryKey)!

// props
defineProps({
    className: { required: false, type: String, default: 'p-container', },
})
// data
provide(conditionKey, ref<Condition>({ keyword: '', page: 1, }))

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
@import "../../../sass/foundation/_breakpoints.scss";

// 各ページでコンテンツを表示するwidthスタイルを設定
// 適宜propsで変更する
.p-container {
    max-width: 1440px;
    &--top {
        max-width: 100%;
        margin-top: 50px;
    }
    &__steps-list {
        max-width: 1000px;
        position: relative;
    }
    &--steps-form {
        max-width: 1440px;
        margin-bottom: 50px;
        @include pc() {
            margin-bottom: 250px;
        }
    }
    &--steps-show {
        display: flex; // メインコンテンツとサイドバーの横並び
        justify-content: space-between;
        max-width: 1000px;
        margin-bottom: 50px;
        @include pc() {
            margin-bottom: 300px;
        }
    }
    &__mypage {
        max-width: 600px;
        margin-bottom: 50px;
        @include pc() {
            margin-bottom: 300px;
        }
    }
}
</style>
