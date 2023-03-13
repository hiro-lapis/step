import axios from './baseRepository'

/**
 * Step CRUDリポジトリ
 */
export class StepRepository {
    private readonly baseUrl = '/api/steps'

    async store(data: { category_id: number, achievement_time_type_id: number,  name: string }): Promise<any> {
        return await axios.post(`${this.baseUrl}`, data)
    }
    async get(data: { key_word: string, category_id: number|null, achievement_time_type_id: number|null }): Promise<any> {
        // クエリパラメータで送信
        return await axios.get(`${this.baseUrl}`, { params: data })
    }
}
