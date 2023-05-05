import axios from './baseRepository'
import { AxiosResponse } from 'axios'

/**
 * chat gpt API リポジトリ
 */
export class ChatGptRepository {
    private readonly completionUrl = '/api/chat-gpt/completion'

    async completion(title: string, prompt: string): Promise<AxiosResponse<CompletionParams>> {
        const params = {
            title: title,
            prompt: prompt,
        }
        return await axios.post(`${this.completionUrl}`, params)
    }
}

type CompletionParams = {
    message: string,
    remain_count: number,
}
