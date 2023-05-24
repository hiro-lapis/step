<script setup lang="ts">
import { computed, inject, PropType } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import CategoryBadge from './CategoryBadge.vue'
import EditIcon from './EditIcon.vue'
import StepProgressionCountBadge from './StepProgressionCountBadge.vue'
import SubStepCountBadge from './SubStepCountBadge.vue'
import { useTypeGuards } from '../../composables/typeGuards'

/**
 * ステップカード
 * widthは親コンポーネントに任せる。単体・複数カラム両方の形式応じて幅が変わるため
 */

// utilities
const userStore = useUserStore()
const router = useRouter()
// props
const props = defineProps({
    step : { require: true, type: Object as PropType<Step|ChallengeStep>, },
    challengeMode: { require: false, type: Boolean, default: false,},
})
// computed
// カードクリック時の遷移先を返す
const getShowRoute = computed(() => {
    if (props.challengeMode && isChallengeStep(props.step!)) {
        return { name: 'challenge-steps-show', params: { id: props.step.id } }
    }
    return { name: 'steps-show', params: { id: props.step!.id } }
})
// 親コンポーネントから編集アイコン表示フラグを受け取る
const editIcon = inject('editIcon', false)
const showEditIcon = () => {
    return editIcon && userStore.isLogin && !isChallengeStep(props.step!) && userStore.user.id === props.step!.user_id
}
// methods
const { isChallengeStep } = useTypeGuards()
const getStatusName = (step: Step|ChallengeStep) => isChallengeStep(step) ? step.status_name : ''
const moveToEditPage = (stepId: number) => {
    router.push({ name: 'steps-edit', params: { id: stepId } })
}
</script>

<template>
    <router-link :to="getShowRoute" class="c-step-card">
        <EditIcon v-if="showEditIcon()"
            :stepId="step?.id"
            :clickFunc="moveToEditPage"
            classname="c-step-card__edit-icon"
            v-tooltip="{ content: '編集', placement: 'bottom',tooltipClass: 'c-tooltip--gray' }"
        />
        <span v-if="step!.is_cleared!" class="c-step-card__cleared-icon">
            <span class="c-icon--cleared__container">
                <span class="c-icon--cleared material-symbols-outlined">new_releases</span>クリア済
            </span>
        </span>
        <span v-if="step!.is_challenged!" class="c-step-card__challenged-icon">
            <span class="c-icon--challenged__container">
                <span class="c-icon--challenged material-symbols-outlined">brightness_empty</span>挑戦中
            </span>
        </span>
        <div class="c-step-card__head">
            <img :src="step!.image_url" alt="" class="c-img--step-card">
        </div>
        <div class="c-step-card__body">
            <h2 class="c-step-card__title u-spread">{{ step!.name }}</h2>
            <p class="c-step-card__txt u-spread">
                <div class="u-margin-b-05p">
                    <CategoryBadge :id="step!.category_id" />
                    <!-- チャレンジ進捗 -->
                    <template v-if="isChallengeStep(step!)">
                        <span class="u-margin-l-1p">
                            <StepProgressionCountBadge :step="step!" />
                        </span>
                    </template>
                    <template v-else>
                        <!-- サブステップ数 -->
                        <span class="u-margin-l-1p">
                            <SubStepCountBadge :step="step!" />
                        </span>
                    </template>
                    <span v-if="challengeMode" class="u-margin-l-1p">
                        {{ getStatusName(step!) }}
                    </span>
                </div>
                <span>
                    達成目安時間: {{ step!.achievement_time! }}
                </span>
            </p>
        </div>
    </router-link>
</template>

<style scoped lang="scss">
</style>
