import axios from './baseRepository'

/**
 * Step CRUDリポジトリ
 */
export class StepRepository {
    private readonly storeUrl = '/api/steps'

    async store(data: { category_id: number, achievement_time_type_id: number,  name: string }): Promise<any> {
        return await axios.post(`${this.storeUrl}`, data)
    }
}
