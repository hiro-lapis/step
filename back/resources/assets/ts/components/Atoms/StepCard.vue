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
        return { name: 'challenge-steps-show', params: { id: props.step.step_id } }
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
            <!-- <span v-if="showEditBtn"> -->
                <!-- <router-link :to="{ name: 'steps-edit', params: { id: step!.id}}"> -->
                    <!-- <i class="c-icon--edit u-float-shadow fa fa-pen"></i> -->
                    <!-- <span class="c-step-card__edit-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span> -->
                <!-- </router-link> -->
            <!-- </span> -->
        </p>
    </router-link>
</template>

<style scoped lang="scss">
.c-step-card {
    position: relative;
	display: block;
	border: 1px solid #e7e7e7;
	border-radius: 5px;
	color: inherit;
	text-decoration: none;
	transition: all .3s;
    padding: 10px 20px;
    &__title {
        margin-bottom: 10px;
        background-repeat: no-repeat;
        background-size: 100% 10%;
        background-position: bottom;
        font-weight: bold;
        padding: 5px;
        background-image: linear-gradient(135deg, #FFE985 10%, #FA742B 100%);
    }
    &__txt {
        font-size: 12px;
    }
    &:hover .c-step-card__title {
        opacity: 0.7;
    }
    &__edit-icon {
        cursor: pointer;
        color: #f6f5f4;
        background-color: #666;
        // カードの右上に配置する
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px;
        border-radius: 50%;
    }
}
</style>
