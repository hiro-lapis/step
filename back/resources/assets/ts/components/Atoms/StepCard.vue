<script setup lang="ts">
import { computed, inject, PropType } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import CategoryBadge from './CategoryBadge.vue'
import CustomBadge from './CustomBadge.vue'
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
const { isChallengeStep, isDraftStep } = useTypeGuards()
// props
const props = defineProps({
    step : { required: true, type: Object as PropType<Step|ChallengeStep>, },
    challengeMode: { required: false, type: Boolean, default: false,},
})
// computed
// カードクリック時の遷移先を返す
const getShowRoute = computed(() => {
    if (props.challengeMode && isChallengeStep(props.step!)) {
        return { name: 'challenge-steps-show', params: { id: props.step.id } }
    }
    // 下書きの場合は編集ページへ遷移
    if (isDraftStep(props.step!)) {
        return { name: 'steps-edit', params: { id: props.step!.id } }
    }
    return { name: 'steps-show', params: { id: props.step!.id } }
})
// 親コンポーネントから編集アイコン表示フラグを受け取る
const editIcon = inject('editIcon', false)
const showEditIcon = () => {
    return editIcon && userStore.isLogin && !isChallengeStep(props.step!) && userStore.user.id === props.step!.user_id
}
const editTooltopText = computed(() => {
    return isDraftStep(props.step) ? '下書きを編集' : 'ステップを編集'
})
// methods

const getStatusName = (step: Step|ChallengeStep) => isChallengeStep(step) ? step.status_name : ''
const moveToEditPage = () => {
    router.push({ name: 'steps-edit', params: { id: props.step!.id } })
}
</script>

<template>
    <router-link :to="getShowRoute" class="c-step-card">
        <EditIcon v-if="showEditIcon()"
            :stepId="step?.id"
            :clickFunc="moveToEditPage"
            classname="c-step-card__edit-icon"
            v-tooltip="{ content: editTooltopText, placement: 'bottom', tooltipClass: 'c-tooltip--gray' }"
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
                    <template v-if="step!.category_id">
                        <CategoryBadge :id="step!.category_id" class="u-margin-r-1p" />
                    </template>
                    <!-- 下書きバッジ -->
                    <div class="c-step-card__draft-badge">
                        <CustomBadge :step="step" mode="draft" />
                    </div>
                    <!-- チャレンジ進捗 -->
                    <template v-if="isChallengeStep(step!)">
                        <span class="u-margin-l-1p">
                            <StepProgressionCountBadge :step="step!" />
                        </span>
                    </template>
                    <template v-else>
                        <!-- サブステップ数 -->
                        <span :class="{ 'u-margin-l-1p' : step!.category_id }">
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
