<script setup lang="ts">
import { computed, PropType } from 'vue'
import { Step } from '../../types/Step'
import { ChallengeStep } from '../../types/ChallengeStep'
import { useTypeGuards } from 'assets/ts/composables/typeGuards'

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, },
    showProgress: { required: false, type: Boolean, default: false },
})

// computed
const label = computed(() => 'ステップ数:' + (props.step as Step).sub_steps_count!)
</script>

<template>
    <span class="c-badge--step-count">{{ label }}</span>
</template>

<style lang="scss" scoped></style>
