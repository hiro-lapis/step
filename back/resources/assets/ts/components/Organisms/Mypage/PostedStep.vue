<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { Step } from '../../../types/Step'
import { useRequestStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import StepCardColumn from '../../Molecules/StepCardColumn.vue'

// utilities
const requestStore = useRequestStore()
const $repositories = inject<Repositories>("$repositories")!

// data
const steps = ref<Step[]>([])

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
</template>

<style scoped lang="scss">
.c-posted-step {
    // margin-top: 20px;
}
</style>
