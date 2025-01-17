<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import ImageClip from '../../Atoms/ImageClip.vue'
import { Step } from '../../../types/Step'
import { repositoryKey } from '../../../types/common/Injection'

const $repositories = inject<Repositories>(repositoryKey)!
const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const userStore = useUserStore()
const route = useRoute()
const router = useRouter()

// data
const step = ref<Step>({
    id: Number(route.params.id) ?? 0,
    category_id: 0,
    user_id: 0,
    name: '',
    summary: '',
    image_url: '',
    sub_steps: [],
    achievement_time_type_id: 0,
    time_count: 0,
    achievement_time_type: { id: 0, name: '', display_name: ''},
    category: { id: 0, name: ''},
})
const isInitialized = ref(false)
// computed
const isChallengeable = computed(() => {
    // ロード完了後で、ログイン中で、投稿ユーザーでなく、チャレンジ中でもないか
    return isInitialized.value
        && userStore.isLogin
        && step.value.is_cleared === false
        && userStore.user.id !== step.value.user_id
        && inNotInChallenge.value
})

const inNotInChallenge = computed(() => {
    return userStore.isInChallenge(Number(route.params.id)) === false
})
// 未ログインで初期ロード後
const showLoginBtn = computed(() => {
    return isInitialized.value && userStore.isLogin === false
})

// methods
const fetchData = async () => {
    const stepId = Number(route.params.id)
    requestStore.setLoading(true)
    await $repositories.step.find(stepId)
        .then(response => {
            step.value = response.data

            isInitialized.value = true
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
// ログインユーザーがステップへチャレンジ
const challenge = async () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.step.challenge(step.value.id!)
        .then(response => {
            requestStore.setLoading(false)
            // ステップを挑戦中のステップに加えボタンを非表示
            userStore.setChallengingStepId(step.value.id!)
            messageStore.setMessage(response.data.message)
            gotoChallengeStepShow(response.data.challenge_step_id)
        })
}
// ログインページへ遷移
const gotoLogin = () => router.push({ name: 'login'})
onMounted(() => {
    fetchData()
})
// 3s待ってから一覧画面へ遷移
const gotoStepList = () => {
    setTimeout(() => {
        router.push({ name: 'steps-list' })
    }, 3000)
}
// 3s待ってから挑戦中のステップ画面へ遷移
const gotoChallengeStepShow = (challengeStepId: number) => {
    setTimeout(() => {
        router.push({ name: 'challenge-steps-show', params: { id: challengeStepId } })
    }, 3000)
}
</script>

<template>
    <BaseView className="p-container--steps-show">
        <template v-slot:content>
            <div class="p-step-show__main">
                <StepPreview
                    :step="step"
                    readOnly
                    @delete="gotoStepList"
                >
                    <template v-slot:bottom>
                        <button
                            v-if="isChallengeable"
                            @click="challenge"
                            class="c-btn--challenge">
                            挑戦する!
                        </button>
                        <button
                            v-if="showLoginBtn"
                            @click="gotoLogin"
                            class="c-btn--challenge">
                            ログインして挑戦する!
                        </button>
                    </template>
                </StepPreview>
            </div>
            <div v-if="isInitialized" class="p-step-show__aside">
                <div class="c-step-supplement">
                    <div class="c-step-supplement__head">
                        <ImageClip :path="step.user_image_url!"></ImageClip>
                        <div class="u-margin-l-05p">{{ step.user_name! }}</div>
                    </div>
                    <div class="c-step-supplement__user-information">
                        {{ step.user_profile! }}
                    </div>
                </div>
            </div>
        </template>
    </BaseView>
</template>

<style scoped lang="scss">
</style>
