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
        <template :key="i" v-for="(step, i) in stepList">
            <li class="c-step-card-column__item">
                <StepCard challengeMode :step="step" />
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">
</style>
