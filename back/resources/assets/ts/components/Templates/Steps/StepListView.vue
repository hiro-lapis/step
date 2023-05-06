<script setup lang="ts">
import { inject, onMounted, ref, reactive } from 'vue'
import { useRequestStore } from '../../../store/globalStore'
import StepCardList from '../../Molecules/StepCardList.vue'
import PaginationList from '../../Atoms/PaginationList.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'

// utilities
const requestStore = useRequestStore()
const $repositories = inject<Repositories>('$repositories')!

// props
// data
const stepList = ref<Step[]>([])
const condition = reactive({
    key_word: '',
    page: 1 ,
})
const paginationInfo = reactive({
    current_page: 1,
    last_page: 1,
    total: 1,
})
const changePage = async (page: number) => {
    condition.page = page
    await fetchData()
}
// methods
const fetchData = async () => {
    requestStore.setLoading(true)
    await $repositories.step.get(condition)
        .then(response => {
            stepList.value = response.data.result.data
            paginationInfo.current_page = response.data.result.current_page
            paginationInfo.last_page = response.data.result.last_page
            paginationInfo.total = response.data.result.total
        }).finally(() =>
            requestStore.setLoading(false)
        )
}
const init = () => {
    fetchData()
}
onMounted(() => init())
</script>

<template>
    <BaseView className="p-container__steps-list">
        <template v-slot:content>
            <div class="p-steps-list__body">
                <StepCardList
                    :stepList="stepList"
                />
            </div>
            <div class="p-steps-list__pagination">
                <PaginationList
                    @update:current-page="changePage"
                    :currentPage="paginationInfo.current_page"
                    :lastPage="paginationInfo.last_page"
                    :total="paginationInfo.total"
                />
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
@import "../../../../sass/foundation/_breakpoints.scss";

.p-steps-list {
    &__body {
        margin-bottom: 30px;
        padding-bottom: 0px;
        @include mq() {
            padding-bottom: 45px;
        }
    }
    &__pagination {
        width: 100%;
        height: 30px;
        justify-content: center;
        display: flex;
        position: absolute; // コンテンツ最下部に固定
        bottom: 0;
    }
}
</style>
