import axios from './baseRepository'
import { AxiosResponse } from 'axios'
import { ChallengeStep } from '../types/ChallengeStep'

/**
 * ChallengeStep/ChallengeSubStep CRUDリポジトリ
 */
export class ChallengeStepRepository {
    private readonly findUrl = '/api/challenge-steps'
    private readonly clearUrl = '/api/challenge-steps'
    async findChallenge(challenge_step_id: number): Promise<AxiosResponse<FindResponse>> {
        return await axios.get(`${this.findUrl}/${challenge_step_id}`)
    }
    async clear(step_id: number): Promise<AxiosResponse<ClearResponse>> {
        return await axios.post(`${this.clearUrl}`, { id: step_id })
    }
}

type FindResponse = ChallengeStep
type ClearResponse = {
    message: string
}
