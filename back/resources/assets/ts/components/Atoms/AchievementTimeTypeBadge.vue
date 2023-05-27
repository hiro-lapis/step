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
const label = computed(() => {
    return AchivementTimeType[props.id]
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
const AchivementTimeType = {
    1: '分',
    2: '時間',
    3: '日',
    4: '週',
    5: '月',
    6: '年',
}
</script>

<template>
    <span @click="clicked">
        <template v-if="id">
            <span
                class="c-badge--category"
                >{{ label }}</span
            >
        </template>
    </span>
</template>
