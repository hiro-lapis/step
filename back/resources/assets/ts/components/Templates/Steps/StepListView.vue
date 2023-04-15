<script setup lang="ts">
import { inject, onMounted, ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import StepCardList from '../../Molecules/StepCardList.vue'
import PaginationList from '../../Atoms/PaginationList.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'

// utilities
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>("$repositories")!
const router = useRouter()
const route = useRoute()

// props
// data
const stepList = ref<Step[]>([])
const condition = reactive({
    key_word: '',
    category_id: null,
    achievement_time_type_id: null,
    page: 1 , // TODO 動的にする
})
const paginationInfo = reactive({
    current_page: 1,
    last_page: 1,
    total: 1,
})
// methods
const fetchData = () => {
    requestStore.setLoading(true)
    $repositories.step.get(condition)
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
            <h1>ステップ一覧</h1>
            <div class="p-steps-list__body">
                <StepCardList
                    :stepList="stepList"
                />
            </div>
            <div class="p-steps-list__pagination">
                <PaginationList
                    :currentPage="paginationInfo.current_page"
                    :lastPage="paginationInfo.last_page"
                    :total="paginationInfo.total"
                />
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
.p-steps-list {
    &__body {
        margin-bottom: 30px;
    }
    &__pagination {
        width: 100%;
        justify-content: center;
        display: flex;
    }
}
</style>
