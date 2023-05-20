<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    isRequired: { required: false, type: Boolean, default: true },
    showOptional: { required: false, type: Boolean, default: true },
})
const text = computed(() => {
    if (props.isRequired) return '必須'
    if (props.showOptional) return '任意'
})
</script>

<template>
    <span class="c-message--requirement" :style="{ backgroundColor: isRequired ? '#d9534f' : '#78909C' }">{{ text }}</span>
</template>
