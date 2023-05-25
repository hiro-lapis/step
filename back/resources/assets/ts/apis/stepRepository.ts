import axios from './baseRepository'
import { AxiosResponse } from 'axios'
import { Step } from '../types/Step'
import { ChallengeStep } from '../types/ChallengeStep'

/**
 * Step CRUDリポジトリ
 */
export class StepRepository {
    private readonly baseUrl = '/api/steps' // 一覧、新規作成
    private readonly draftUrl = '/api/steps/draft'
    private readonly findEditUrl = '/api/steps/{id}/edit'
    private readonly updateUrl = '/api/steps/update'
    private readonly deleteUrl = '/api/steps/delete'
    private readonly showUrl = '/api/steps'
    private readonly challengeFindUrl = '/api/steps'
    private readonly challengeUrl = '/api/steps/challenges'

    async saveDraft(data: {category_id: number, achievement_time_type_id: number, time_count: number, name: string, summary: string }): Promise<any>
    {
        return await axios.post(`${this.draftUrl}`, data)
    }
    async store(data: { category_id: number, achievement_time_type_id: number, time_count: number, name: string, summary: string }): Promise<any> {
        return await axios.post(`${this.baseUrl}`, data)
    }
    async update (data: Step): Promise<AxiosResponse<UpdateResponse>> {
        return await axios.put(`${this.updateUrl}`, data)
    }
    async delete (step_id: number): Promise<any> {
        return await axios.delete(`${this.deleteUrl}`, { data: { id: step_id } })
    }
    async get(data: { key_word: string, page: number , category_id: number|null, order_by: string, desc: string}): Promise<any> {
        // クエリパラメータで送信
        return await axios.get(`${this.baseUrl}`, { params: data })
    }
    async find(step_id: number): Promise<AxiosResponse<FindResponse>> {
        return await axios.get(`${this.showUrl}/${step_id}`)
    }
    async findEdit(step_id: number): Promise<AxiosResponse<FindEditResponse>> {
        return await axios.get(this.findEditUrl.replace('{id}', step_id.toString()))
    }
    async findChallenge(challenge_step_id: number): Promise<AxiosResponse<FindChallengeResponse>> {
        return await axios.get(`${this.challengeFindUrl}/${challenge_step_id}`)
    }
    async challenge(step_id: number): Promise<AxiosResponse<ChallengeResponse>> {
        return await axios.post(`${this.challengeUrl}`, { id: step_id})
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

type FindResponse = Step
type FindEditResponse = Step
type UpdateResponse = {
    status: boolean
    step: Step
}
type FindChallengeResponse = ChallengeStep

type ChallengeResponse = {
    message: string,
    challenge_step_id: number,
}
