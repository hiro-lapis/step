<script setup lang="ts">
import { computed } from 'vue'

// props
const props = defineProps({
    id: { required: true, type: Number, },
})
interface Emits {
    (e: 'click:badgeIcon', value: Number): void;
}
const emit = defineEmits<Emits>()
// data
// computed
const color = computed(() => {
    return ColorTypes[props.id] ?? '#999'
})
const label = computed(() => {
    return CategoryType[props.id]
})

// methods
const clicked = () => {
    emit('click:badgeIcon', props.id)
}
const ColorTypes = {
    1: '#f48fb1', // pink
    2: '#80cbc4', // lightGreen
    3: '#16A5DE', // blue
    4: '#87C041', // green
}
const CategoryType = {
    1: 'プログラミング', // pink
    2: '英語', // lightGreen
    3: 'ダイエット', // yellow
    4: '朝活', // green
}
</script>

<template>
    <span @click="clicked">
        <template v-if="id">
            <span
                class="c-badge--category"
                :style="{ backgroundColor: color }"
                >{{ label }}</span
            >
        </template>
        <template v-else>
            <span
                class="c-badge--category"
                :style="{ backgroundColor: color }"
                >{{ label }}</span
            >
        </template>
    </span>
</template>
