import axios from './baseRepository'

/**
 * Step CRUDリポジトリ
 */
export class StepRepository {
    private readonly baseUrl = '/api/steps'

    async store(data: { category_id: number, achievement_time_type_id: number,  name: string, page: number }): Promise<any> {
        return await axios.post(`${this.baseUrl}`, data)
    }
    async get(data: { key_word: string, category_id: number|null, achievement_time_type_id: number|null }): Promise<any> {
        // クエリパラメータで送信
        return await axios.get(`${this.baseUrl}`, { params: data })
    }
}
type PaginationData = {
    current_page: number
    data: Array<{id: number, user_id: number, name: string, achivement_type_id: number, category_name: string }>
    last_page: number
    total: number
}
type GetResponse = {
    result : PaginationData
}
