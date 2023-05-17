<script setup lang="ts">
import { computed, inject, onMounted, provide, ref } from 'vue'
import { Step } from '../../../types/Step'
import { useRequestStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import StepCardColumn from '../../Molecules/StepCardColumn.vue'
import { repositoryKey } from '../../../types/common/Injection'

// utilities
const requestStore = useRequestStore()
const $repositories = inject<Repositories>(repositoryKey)!

// data
const steps = ref<Step[]>([])
provide('editIcon', true)
// computed
const showNotExists = computed(() => !requestStore.isLoading && steps.value.length === 0)
const existsSteps = computed(() => steps.value.length > 0)
// methods
const fetchData = () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.mypage.getPostedStep()
        .then(response => {
            steps.value = response.data.steps
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
onMounted(() => {
    fetchData()
})
</script>

<template>
    <template v-if="existsSteps">
        <div class="c-posted-step">
            <StepCardColumn :stepList="steps" />
        </div>
    </template>
    <template v-else-if="showNotExists">
        <p>投稿したステップはありません</p>
    </template>
</template>

<style scoped lang="scss">
</style>
