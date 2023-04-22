<script setup lang="ts">
import { computed, inject, PropType, ref } from 'vue'
import { Step } from '../../types/Step'
import { ChallengeStep } from '../../types/ChallengeStep'

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, },
    showProgress: { required: false, type: Boolean, default: false },
})

// computed

type colors = 'pink'|'lightGreen'|'yellow'|'green'|'blue'
const color = computed(() => {
    if ('challenge_sub_steps_count' in props.step) {
        return '#999'
    }
    return '#999'
})
const label = computed(() => {
    let message = 'サブステップ:'
    if ('challenge_sub_steps_count' in props.step) {
        message += props.step.cleared_sub_steps_count + '/' + props.step.challenge_sub_steps_count
    } else {
        message += props.step.sub_steps_count!
    }
    return message
})
</script>

<template>
    <span
        class="c-badge--step-count"
        >{{ label }}</span>
        <!-- :style="{ backgroundColor: color }" -->
</template>

<style lang="scss" scoped>
// カテゴリーバッジ
.c-badge--category {
    color: #fff;
    padding: 7px 10px;
    // background-color: red; // 切り出し
    border-radius: 2px;
    font-size: 10px;
    display: inline-block;
}
</style>
