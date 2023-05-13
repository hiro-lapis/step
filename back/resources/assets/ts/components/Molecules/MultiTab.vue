<script setup lang="ts">
import { computed, inject, Ref } from 'vue'

interface Tab {
    value: string
    label: string
}
// props
defineProps({
    tabs: { required: true, type: Array as () => Tab[], },
    selectedTab: { required: true, type: String, },
})
// emits
interface Emits {
    (e: 'update:selectedTab', tabName: string): void
}
const emit = defineEmits<Emits>()
// computed
const currentTab = inject<Ref<string>>('currentTab')!
const isPostedStep = (tabName: string) => tabName === 'postedStep'
const isChallengingStep = (tabName: string) => tabName === 'challengingStep'
// inject
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
            >
                {{ tab.label }}
                <!-- 投稿したステップ投稿数表示 -->
                <template v-if="isPostedStep(tab.value)">
                    <!-- TODO: スタイル設定とinjectで値を取得 -->
                    <span class="c-multi-tab__tab-count">5</span>
                </template>
                <!-- チャレンジステップ数表示 -->
                <!-- TODO: スタイル設定とinjectで値を取得 -->
                <template v-if="isChallengingStep(tab.value)">
                    <span class="c-multi-tab__tab-count">15</span>
                </template>
            </label>
        </template>
        <div class="c-multi-tab__body">
            <slot name="content"></slot>
        </div>
    </div>
</template>

<style scoped lang="scss">
</style>
