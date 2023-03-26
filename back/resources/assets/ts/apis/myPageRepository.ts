import axios from './baseRepository'
import { AxiosResponse } from 'axios'
import { User } from '../types/User'

/**
 * マイページAPIリポジトリ
 */
export class MyPageRepository {
    private readonly userUrl = '/api/mypage'
    private readonly updateProfileUrl = '/api/mypage/profile'
    private readonly updatePasswordUrl = '/api/mypage/password'

    async user(): Promise<AxiosResponse<UserResponse>> {
        return await axios.get(`${this.userUrl}`)
    }
    async update(params): Promise<AxiosResponse<UpdateResponse>> {
        return await axios.post(`${this.updateProfileUrl}`, params)
    }
    async passwordUpdate(params: { current_password: string, password: string, password_confirmation: string }): Promise<any> {
        return await axios.post(`${this.updatePasswordUrl}`, params)
    }
}

/**
 * APIレスポンスの型指定
 */
export type UserResponse = {
    user: User
}
export type UpdateResponse = {
    message: string
}
