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
.c-multi-tab *, .c-multi-tab *:before, .c-multi-tab *:after {
    box-sizing: border-box;
}
.c-multi-tab {
    &__radio {
        display: none;
    }
    &__body {
        padding: 15px 10px;
        background: #fff;
        min-height: 150px; // エリアの高さ
    }
    &__label {
        background: #fff;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 6px 6px 0 0;
        &.active {
            font-weight: bold;
            border-bottom: 3px solid #ff914d;
        }
    }
    &__panel {
        position: absolute;
        width: 100%;
        opacity: 0;
        padding: 0.5em 1em;
        transform: translateY(-10px);
        transition: opacity 0.5s, -webkit-transform 0.5s;
        transition: transform 0.5s, opacity 0.5s;
        transition: transform 0.5s, opacity 0.5s, -webkit-transform 0.5s;
        opacity: 1;
        transform: translateY(0px);
        &--1 {
            @extend .c-multi-tab__panel;
            background: #80CBC4;
        }
    }
}
</style>
