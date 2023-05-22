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
        from: 1,
        to: 1,
        current_page: 1,
        last_page: 1,
        total: 1,
    })
    // methods
    const fetchData = async (condition : { key_word: string, page: number , category_id: number|null, order_by: string, desc: boolean}) => {
        const params = {
            key_word: condition.key_word,
            page: condition.page,
            category_id: condition.category_id? condition.category_id : null,
            order_by: condition.order_by,
            desc: condition.desc ? '1' : '0',
        }

        await $repositories.step.get(params)
            .then(response => {
                stepList.value = response.data.result.data
                pageInfo.value.from = response.data.result.from
                pageInfo.value.to = response.data.result.to
                pageInfo.value.current_page = response.data.result.current_page
                pageInfo.value.last_page = response.data.result.last_page
                pageInfo.value.total = response.data.result.total
            }).catch(error => {
                return
            })
    }
    return {
        stepList,
        pageInfo,
        fetchData,
    }
})
