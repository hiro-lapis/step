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
    async update(data: { name: string, email: string }, files: File|null): Promise<AxiosResponse<UpdateResponse>> {
        /**
         * パラメータを設定
         * 画像アップロードとフォームデータを同時送信する場合、下記に注意
         * ・FormDataオブジェクトを使う(ファイルアップロード時の作法)
         */
        let params = new FormData()
        params.append('name', data.name)
        params.append('email', data.email)
        if (!!files) {
            params.append('file', files)
        }
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
