<script setup lang="ts">
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'
import StepCard from '../Atoms/StepCard.vue'

// props
defineProps({
    stepList: { require: true, type: Array<Step|ChallengeStep>, },
    challengeMode: { require: false, type: Boolean, default: false,}
})
</script>

<template>
    <ul class="c-step-card-column">
        <template :key="step.id" v-for="step in stepList">
            <li class="c-step-card-column__item">
                <StepCard :step="step" />
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">
.c-step-card-column { // 縦１列カラム
	display: flex;
	flex-direction: column;
	margin-top: -20px; /*1行目の上マージンを相殺*/
    &__item {
        margin-top: 20px;
    }
}
</style>
