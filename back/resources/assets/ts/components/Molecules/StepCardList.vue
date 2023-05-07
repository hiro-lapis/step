<script setup lang="ts">
import { computed } from 'vue'
import { Step } from '../../types/Step'
import StepCard from '../Atoms/StepCard.vue'
import { useRequestStore } from '../../store/globalStore'

// utilities
const requestStore = useRequestStore()
// props
const props = defineProps({
    stepList: { require: true, type: Array<Step>, }
})
// computed
const isEmpty = computed(() => props.stepList!.length === 0)
const showNoDataMessage = computed(() => isEmpty.value && !requestStore.isLoading)
</script>

<template>
    <ul class="c-step-card-list">
        <template :key="i" v-for="(step, i) in stepList">
            <li class="c-step-card-list__item">
                <StepCard :step="step" />
            </li>
        </template>
        <template v-if="showNoDataMessage">
            <p>検索にマッチする情報が見つかりませんでした</p>
        </template>
    </ul>
</template>

<style scoped lang="scss">
</style>
