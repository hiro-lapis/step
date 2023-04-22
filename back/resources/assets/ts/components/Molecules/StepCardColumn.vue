<script setup lang="ts">
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import CategoryBadge from '../Atoms/CategoryBadge.vue'
import StepCountBadge from '../Atoms/StepCountBadge.vue'
import { useTypeGuards } from '../../composables/typeGuards'

// props
const props = defineProps({
    stepList: { require: true, type: Array<Step|ChallengeStep>, },
    challengeMode: { require: false, type: Boolean, default: false,}
})
// computed
// methods
const stepMerit = (merit: string|null) => {
    if (merit === null) {
        return '　'
    }
return merit
}
const { isChallengeStep } = useTypeGuards()
const getStatusName = (step: Step|ChallengeStep) => {
    if (isChallengeStep(step)) {
        return step.status_name
    }
    return ''
}
</script>

<template>
    <ul class="c-step-card-column">
        <template :key="step.id" v-for="step in stepList">
            <li class="c-step-card-column__item">
                <!-- ステップorチャレンジ中ステップ詳細表示情報に合わせて遷移先変更 -->
                <router-link :to="isChallengeStep(step) ? { name: 'challenge-steps-show', params: { id: step.id } } : { name: 'steps-show', params: { id: step.id } }" class="c-step-card">
                    <div class="c-step-card__head">
                    </div>
                    <h2 class="c-step-card__title u-spread">{{ step.name }}</h2>
                    <p class="c-step-card__txt u-spread">
                        <p>{{ stepMerit(step.merit!) }}</p>
                        <div class="u-margin-b-05p">
                            <CategoryBadge :id="step.category_id" />
                            <span class="u-margin-l-1p">
                                <StepCountBadge :step="step" />
                            </span>
                            <span v-if="challengeMode" class="u-margin-l-1p">
                                {{ getStatusName(step) }}
                            </span>
                        </div>
                        <span>
                            達成目安時間: {{ step.achievement_time_type!.name }}
                        </span>
                    </p>
                </router-link>
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">

.c-step-card-column {
	display: flex;
	flex-direction: column;
	margin-top: -20px; /*1行目の上マージンを相殺*/
    &__item {
        margin-top: 20px;
    }
}


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
