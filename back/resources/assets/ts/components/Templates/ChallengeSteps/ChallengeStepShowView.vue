<script setup lang="ts">
// 挑戦中のステップを表示するページ
// ステップへの挑戦後にステップを編集してサブステップが更新されたり削除されたりした場合を考慮して、画面を別に作る
import { inject, onMounted, ref } from 'vue'
import { useRequestStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import { useRoute } from 'vue-router'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import { ChallengeStep } from '../../../types/ChallengeStep'
import ImageClip from '../../Atoms/ImageClip.vue'
import { repositoryKey } from '../../../types/common/Injection'

const $repositories = inject<Repositories>(repositoryKey)!
const requestStore = useRequestStore()
const route = useRoute()

// data
const step = ref<ChallengeStep>()
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
onMounted(() => {
    fetchData()
})
</script>

<template>
    <BaseView className="p-container--steps-show">
        <template v-slot:content>
            <div class="p-challenge-step-show__main">
                    <template v-if="step">
                        <StepPreview
                            @clear="fetchData"
                            :step="step"
                            :challengeMode="true"
                        ></StepPreview>
                </template>
            </div>
            <div v-if="isInitialized && step" class="p-challenge-step-show__aside">
                <div class="p-challenge-step-show__supplement">
                    <div class="p-challenge-step-show__supplement__head">
                        <ImageClip :path="step.post_user_image_url!"></ImageClip>
                        <div class="u-margin-l-05p">{{ step!.post_user_name! }}</div>
                    </div>
                    <div class="p-challenge-step-show__supplement__user-information">
                        {{ step.post_user_profile! }}
                    </div>
                </div>
            </div>
        </template>
    </BaseView>
</template>

<style scoped lang="scss">
</style>
