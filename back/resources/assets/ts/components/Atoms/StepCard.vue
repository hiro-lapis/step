<script setup lang="ts">
import { computed, PropType } from 'vue'
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import CategoryBadge from './CategoryBadge.vue'
import StepProgressionCountBadge from './StepProgressionCountBadge.vue'
import SubStepCountBadge from './SubStepCountBadge.vue'
import { useTypeGuards } from '../../composables/typeGuards'
import { useUserStore } from '../../store/globalStore'

/**
 * ステップカード
 * widthは親コンポーネントに任せる。単体・複数カラム両方の形式応じて幅が変わるため
 */

// utilities
const userStore = useUserStore()

// props
const props = defineProps({
    step : { require: true, type: Object as PropType<Step|ChallengeStep>, },
    challengeMode: { require: false, type: Boolean, default: false,}
})
// computed
const showEditBtn = computed(() => {
    if (props.challengeMode || isChallengeStep(props.step!)) return false
    return userStore.user.id === props.step!.user_id
})
// カードクリック時の遷移先を返す
const getShowRoute = computed(() => {
    if (props.challengeMode && isChallengeStep(props.step!)) {
        return { name: 'challenge-steps-show', params: { id: props.step.id } }
    }
    return { name: 'steps-show', params: { id: props.step!.id } }
})

// methods
const { isChallengeStep } = useTypeGuards()
const getStatusName = (step: Step|ChallengeStep) => isChallengeStep(step) ? step.status_name : ''
</script>

<template>
    <router-link :to="getShowRoute" class="c-step-card">
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
                達成目安時間: {{ step!.achievement_time_type!.name }}
            </span>
        </p>
    </router-link>
</template>

<style scoped lang="scss">
</style>
