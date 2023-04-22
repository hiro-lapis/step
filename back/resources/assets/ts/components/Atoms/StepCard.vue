<script setup lang="ts">
import { PropType } from 'vue'
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import { ChallengeStatusJudgement } from '../../composables/ChallengeStatus'
import CategoryBadge from './CategoryBadge.vue'
import StepCountBadge from './StepCountBadge.vue'


// props
const props = defineProps({
    step : { require: true, type: Object as PropType<Step|ChallengeStep>, },
    challengeMode: { require: false, type: Boolean, default: false,}
})
// computed
// カードクリック時の遷移先を返す
const getRoute = (step: Step|ChallengeStep) => {
    if (props.challengeMode && isChallengeStep(step)) {
        return { name: 'challenge-step', params: { id: step.step_id } }
    }
    return { name: 'step', params: { id: step.id } }
}
// methods
const stepMerit = (merit: string|null) => {
    if (merit === null) {
        return '　'
    }
return merit
}
const isChallengeStep = (step: Step|ChallengeStep): step is ChallengeStep => {
    return (step as ChallengeStep).challenge_user_id !== undefined
}
const getStatusName = (step: Step|ChallengeStep) => {
    if (isChallengeStep(step)) {
        return step.status_name
    }
    return ''
}
</script>

<template>
    <router-link :to="getRoute" class="c-step-card">
        <h2 class="c-step-card__title u-spread">{{ step!.name }}</h2>
        <p class="c-step-card__txt u-spread">
            <p>{{ stepMerit(step!.merit!) }}</p>
            <div class="u-margin-b-05p">
                <CategoryBadge :id="step!.category_id" />
                <span class="u-margin-l-1p">
                    <StepCountBadge :step="step!" />
                </span>
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
.c-step-card {
	display: block;
	border: 1px solid #e7e7e7;
	border-radius: 5px;
	color: inherit;
	text-decoration: none;
	transition: color .3s;
    padding: 10px 20px;
    &:hover {
        color: #999;
    }
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

    :hover .card__head {
        opacity: 0.7;
    }
}
/*hover*/
</style>
