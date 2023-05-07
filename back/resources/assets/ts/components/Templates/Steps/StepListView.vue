<script setup lang="ts">
import { computed, inject, onMounted, Ref } from 'vue'
import { useRequestStore } from '../../../store/globalStore'
import { useStepListStore } from '../../../store/stepListStore'
import StepCardList from '../../Molecules/StepCardList.vue'
import PaginationList from '../../Atoms/PaginationList.vue'
import { conditionKey } from '../../../types/common/Injection'
import { Condition } from '../../../types/components/Condition'

// utilities
const requestStore = useRequestStore()
const stepListStore = useStepListStore()
// data
// headerと共有する検索条件
const condition = inject<Ref<Condition>>(conditionKey)!
const getStepList = computed(() => stepListStore.stepList)

// methods
const search = async () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await stepListStore.fetchData(condition.value)
    requestStore.setLoading(false)
}
const changePage = async (page: number) => {
    condition.value.page = page
    await search()
}
const init = () => search()
onMounted(() => init())
</script>

<template>
    <BaseView className="p-container__steps-list">
        <template v-slot:content>
            <div class="p-steps-list__body">
                <StepCardList
                    :stepList="getStepList"
                />
            </div>
            <div class="p-steps-list__pagination">
                <PaginationList
                    @update:current-page="changePage"
                    :currentPage="stepListStore.pageInfo.current_page"
                    :lastPage="stepListStore.pageInfo.last_page"
                    :total="stepListStore.pageInfo.total"
                />
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
</style>
