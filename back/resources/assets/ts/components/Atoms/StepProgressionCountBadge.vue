<script setup lang="ts">
import { computed, PropType } from 'vue'
import { Step } from '../../types/Step'
import { ChallengeStep } from '../../types/ChallengeStep'

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, },
    showProgress: { required: false, type: Boolean, default: false },
})

// computed
const label = computed(() => {
    let message = 'サブステップ:'
    if ('challenge_sub_steps_count' in props.step) {
        message += props.step.cleared_sub_step_count + '/' + props.step.challenge_sub_steps_count
    } else {
        message += props.step.sub_steps_count!
    }
    return message
})
</script>

<template>
    <span class="c-badge--step-count">{{ label }}</span>
</template>
