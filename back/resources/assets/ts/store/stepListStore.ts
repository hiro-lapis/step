import { inject, ref } from 'vue'
import { defineStore } from 'pinia'
import { Step } from '../types/Step'
import { Repositories } from '../apis/repositoryFactory'
import { repositoryKey } from '../types/common/Injection'

export const useStepListStore = defineStore('stepList', () => {
    const $repositories = inject<Repositories>(repositoryKey)!
    // data store.$reset()で初期化
    const stepList = ref<Step[]>([])
    const pageInfo = ref({
        current_page: 1,
        last_page: 1,
        total: 1,
    })
    // methods
    const fetchData = async (condition : { key_word: string, page: number }) => {
        await $repositories.step.get(condition)
            .then(response => {
                stepList.value = response.data.result.data
                pageInfo.value.current_page = response.data.result.current_page
                pageInfo.value.last_page = response.data.result.last_page
                pageInfo.value.total = response.data.result.total
            })
    }
    return {
        stepList,
        pageInfo,
        fetchData,
    }
})
