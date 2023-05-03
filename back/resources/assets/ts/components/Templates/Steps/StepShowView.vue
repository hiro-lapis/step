<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import ImageClip from '../../Atoms/ImageClip.vue'
import { Step } from '../../../types/Step'

const $repositories = inject<Repositories>('$repositories')!
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
    merit: '',
    sub_steps: [],
    achievement_time_type_id: 0,
    achievement_time_type: { id: 0, name: ''},
    category: { id: 0, name: ''},
})
const isInitialized = ref(false)
// computed
const isChallengeable = computed(() => {
    // ロード完了後で、ログイン中で、投稿ユーザーでなく、チャレンジ中でもないか
    return isInitialized.value
        && userStore.isLogin
        && userStore.user.id !== step.value.user_id
        && inNotInChallenge.value
})

const inNotInChallenge = computed(() => {
    return userStore.isInChallenge(Number(route.params.id)) === false
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
            console.log(response)
            messageStore.setMessage(response.data.message)
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
// ログインページへ遷移
const gotoLogin = () => router.push({ name: 'login'})
onMounted(() => {
    fetchData()
})
</script>

<template>
    <BaseView className="p-container--steps-show">
        <template v-slot:content>
            <div class="p-step-show__main">
                <StepPreview
                    :step="step"
                    readOnly
                >
                    <template v-slot:bottom>
                        <button
                            v-if="isChallengeable"
                            @click="challenge"
                            class="c-btn--challenge">
                            挑戦する!
                        </button>
                        <button
                            v-if="!userStore.isLogin"
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
@import '../../../../sass/foundation/breakpoints';

.p-step-show {
    &__container {
        overflow: hidden; // 見出し線など溢れるデザインを非表示
    }
    &__main {
        width: 100%;
        @include pc() {
            width: 70%;
        }
    }
    &__aside { // sp非表示
        display: none;
        @include pc() {
            width: 25%;
            display: block;
        }
    }
}
.c-step-supplement {
    background-color: #fff;
    padding: 20px;
    &__head {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    &__user-information {
        line-height: 1.5;
        font-size: 12px;
    }
}
</style>
