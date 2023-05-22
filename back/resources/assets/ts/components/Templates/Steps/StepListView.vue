<script setup lang="ts">
import { computed, inject, onMounted, Ref } from 'vue'
import { useRequestStore } from '../../../store/globalStore'
import { useStepListStore } from '../../../store/stepListStore'
import { conditionKey } from '../../../types/common/Injection'
import { Condition, sortType } from '../../../types/components/Condition'
import StepCardList from '../../Molecules/StepCardList.vue'
import PaginationList from '../../Atoms/PaginationList.vue'
import ItemSelectBox from '../../Atoms/ItemSelectBox.vue'

// utilities
const requestStore = useRequestStore()
const stepListStore = useStepListStore()
// data
// headerと共有する検索条件
const condition = inject<Ref<Condition>>(conditionKey)!
const getStepList = computed(() => stepListStore.stepList)

// computed
// 並び順セレクトボックス
const sortItems = computed(() => {
    return Object.keys(sortType).map(key => {
        return { value: key, text: sortType[key].text } as { value: string, text: string }
    })
})
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
            <div class="p-step-list__body">
                <template v-if="getStepList.length > 0">
                    <div class="p-step-list__page-info">
                        <span>並び順:</span>
                        <ItemSelectBox
                            :items="sortItems"
                            v-model:value="condition.sort_type"
                            class="u-margin-r-1p"
                            @change="search()"
                        />
                        <span class="p-step-list__counts">{{ stepListStore.pageInfo.from }}~{{ stepListStore.pageInfo.to }}/{{ stepListStore.pageInfo.total }}件</span>
                    </div>
                </template>
                <StepCardList
                    :stepList="getStepList"
                />
            </div>
            <div class="p-step-list__pagination">
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
