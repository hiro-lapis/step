<script setup lang="ts">
import { computed, inject } from 'vue'

// props
const props = defineProps({
    id: { required: true, type: Number, }
})
interface Emits {
    (e: 'click:badgeIcon', value: Number): void;
}
const emit = defineEmits<Emits>()

// computed
type colors = 'pink'|'lightGreen'|'yellow'|'green'|'blue'
const color = computed(() => {
    return ColorTypes[props.id] ?? '#999'
})

const categories = inject<{id: number, name: string}[]>('categories')
const label = (categoryId: number) => {
    return categories?.find(category => category.id === categoryId)?.name
}

// methods
const clicked = () => {
    emit('click:badgeIcon', props.id)
}
const ColorTypes = {
    1: '#f48fb1', // pink
    2: '#80cbc4', // lightGreen
    3: '#ffff00', // yellow
    4: '#87C041', // green
    5: '#16A5DE', // blue
}
</script>

<template>
    <span @click="clicked">
        <template v-if="id" @click="clickCategory">
            <span
                class="c-badge--category"
                :style="{ backgroundColor: color }"
                >{{ label(id) }}</span
            >
        </template>
        <template v-else>
            <span
                class="c-badge--category"
                :style="{ backgroundColor: color }"
                >{{ label(id) }}</span
            >
        </template>
    </span>
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
