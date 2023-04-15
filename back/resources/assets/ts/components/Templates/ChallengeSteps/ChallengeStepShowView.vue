<script setup lang="ts">
// 挑戦中のステップを表示するページ
// ステップへの挑戦後にステップを編集してサブステップが更新されたり削除されたりした場合を考慮して、画面を別に作る
import { inject, onMounted, ref } from 'vue'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import { useRoute, useRouter } from 'vue-router'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import { ChallengeStep } from '../../../types/ChallengeStep'
import ImageClip from '../../Atoms/ImageClip.vue'

const $repositories = inject<Repositories>("$repositories")!
const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const userStore = useUserStore()
const route = useRoute()
const router = useRouter()

// data
const step = ref<ChallengeStep>({
    id: 0,
    challenge_user_id: 0,
    challenged_at: '',
    cleared_at: '',
    status: 0,
    status_name: '',
    step_id: 0,
    post_user_id: 0,
    category_id: 0,
    name: '',
    merit: '',
    challenge_sub_steps: [],
    challenge_sub_steps_count: 0,
    cleared_sub_steps_count: 0,
    achievement_time_type_id: 0,
    achievement_time_type: { id: 0, name: ''},
    category: { id: 0, name: ''},
})
const isInitialized = ref(false)
// computed

// methods
const fetchData = async () => {
    const stepId = Number(route.params.id)
    requestStore.setLoading(true)
    await $repositories.challengeStep.find(stepId)
        .then(response => {
            step.value = response.data
            isInitialized.value = true
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
// ログインユーザーがサブステップをクリア
// const clear = async (subStepId: number) => {
//     if (requestStore.isLoading) return
//     requestStore.setLoading(true)
//     await $repositories.step.clear(subStepId)
//         .then(response => {
//             console.log(response)
//             messageStore.setMessage(response.data.message)
//         }).finally(() => {
//             requestStore.setLoading(false)
//         })
// }

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
                >
                    <template v-slot:bottom>
                        <!-- コンテンツ下部スロット -->
                    </template>
                </StepPreview>
            </div>
            <div v-if="isInitialized" class="p-step-show__aside">
                <div class="c-step-supplement">
                    <div class="c-step-supplement__head">
                        <ImageClip :path="step.post_user_image_url!"></ImageClip>
                        <div class="u-margin-l-05p">{{ step!.post_user_name! }}</div>
                    </div>
                    <div class="c-step-supplement__user-information">
                        {{ step.post_user_profile! }}
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
