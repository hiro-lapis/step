import axios from './baseRepository'
import { AxiosResponse } from 'axios'
import { ChallengeStep } from '../types/ChallengeStep'

/**
 * ChallengeStep/ChallengeSubStep CRUDリポジトリ
 */
export class ChallengeStepRepository {
    private readonly findUrl = '/api/challenge-steps'
    private readonly clearUrl = '/api/challenge-steps/clear'
    async find(challenge_step_id: number): Promise<AxiosResponse<FindResponse>> {
        return await axios.get(`${this.findUrl}/${challenge_step_id}`)
    }
    async clear(params : ClearParams): Promise<AxiosResponse<ClearResponse>> {
        return await axios.put(this.clearUrl, params)
    }
}

type ClearParams = {
    challenge_step_id: number,
    challenge_sub_step_id: number,
}
type FindResponse = ChallengeStep
type ClearResponse = {
    message: string
}
