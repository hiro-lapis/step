<script setup lang="ts">
interface Tab {
    value: string
    label: string
}
defineProps({
    tabs: { required: true, type: Array as () => Tab[], },
    selectedTab: { required: true, type: String, },
})
interface Emits {
    (e: 'update:selectedTab', tabName: string): void
}
const emit = defineEmits<Emits>()
</script>

<template>
    <div class="c-multi-tab">
        <template :key="tab.value" v-for="tab in tabs">
            <input
                class="c-multi-tab__radio"
                type="radio"
                :id="tab.value"
                :name="tab.value"
                :value="tab.value"
                :checked="selectedTab === tab.value"
                @change="emit('update:selectedTab', tab.value)"
            >
            <label
                :for="tab.value"
                class="c-multi-tab__label"
                :class="selectedTab === tab.value ? 'active' : ''"
            >{{ tab.label }}</label>
        </template>
        <div class="c-multi-tab__body">
            <slot name="content"></slot>
        </div>
    </div>
</template>

<style scoped lang="scss">
</style>
