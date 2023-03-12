<script setup lang="ts">
import { inject, onMounted, ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import StepCardList from '../../Atoms/StepCardList.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'

// utilities
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>("$repositories")!
const router = useRouter()
const route = useRoute()

console.log('type script and pinia is now!')
// store
// props
// data
const stepList = ref<Step[]>([])
const condition = reactive({
    key_word: '',
    category_id: null,
    achievement_time_type_id: null,
})

// emits
// computed
// watch
// methods
const fetchData = () => {
    requestStore.setLoading(true)
    $repositories.step.get(condition)
    .then(response => {
        stepList.value = response.data.result.data
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
            <StepCardList
                :step-list="stepList"
            />
        </template>
    </BaseView>
</template>
