<script setup lang="ts">
import { computed, } from 'vue'

// props
const props = defineProps({
    path: { required: true, type: String, }, // 画像URL
    alt: { required: false, type: String, default: '画像' }, // alt名
    size: { required: false, type: String, default: 'small' },
})

// computed
const sizes =  ['small', 'medium', 'large']
const sizeClass = computed(() => {
    // 指定なしでsmall表示
    if (!props.size || !sizes.includes(props.size)) return 'c-img--clip--small'
    return 'c-img--clip--' + props.size
})

</script>

<template>
        <div class="c-img--clip">
        <img
            :src="path"
            :alt="alt"
            :class="[sizeClass]"
        />
    </div>
</template>
