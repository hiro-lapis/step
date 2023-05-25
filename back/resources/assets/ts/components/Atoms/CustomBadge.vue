<script setup lang="ts">
import { computed, PropType } from 'vue'
import { useTypeGuards } from '../../composables/typeGuards'
import { ChallengeStep } from '../../types/ChallengeStep'
import { Step } from '../../types/Step'

// utilities
const { isDraftStep } = useTypeGuards()
type Mode = 'draft' | 'challenge'
// props
const props = defineProps({
    step : { required: true, type: Object as PropType<Step|ChallengeStep> },
    mode: { required: false, type: String as PropType<Mode>, default: 'draft' },
    value: { required: false, type: Number, default: 0 },
    customLabel: { required: false, type: String, default: '' },
})

interface Emits {
    (e: 'click:badge', value: Number): void
}
const emit = defineEmits<Emits>()
const clicked = () => emit('click:badge', props.value)
// data
// computed
const Colors = {
    'draft': '#888',
    'challenge': '#80cbc4',
}
const color = computed(() => {
    return Colors[props.mode] ?? '#999'
})
const Labels = {
    'draft': '下書き', // pink
    'challenge': '#80cbc4', // lightGreen
    // 3: '#16A5DE', // blue
    // 4: '#87C041', // green
}
// カスタムラベルが設定されいればそれを返しなければmodeに応じたラベルを返す
const label = computed(() => {
    if (props.customLabel) return props.customLabel
    return Labels[props.mode]
})
</script>

<template>
    <span @click="clicked">
        <template v-if="isDraftStep(step)">
            <span
                class="c-badge--custom"
                :style="{ backgroundColor: color }"
                >{{ label }}</span
            >
        </template>
    </span>
</template>
