import axios from './baseRepository'
import { AxiosResponse } from 'axios'
import { User } from '../types/User'
import { Step } from '../types/Step'

/**
 * マイページAPIリポジトリ
 */
export class MyPageRepository {
    private readonly userUrl = '/api/mypage'
    private readonly updateProfileUrl = '/api/mypage/profile'
    private readonly updatePasswordUrl = '/api/mypage/password'
    private readonly getPostedStepUrl = '/api/mypage/posted-step'
    private readonly getChallengingStepUrl = '/api/mypage/challenging-step'

    async user(): Promise<AxiosResponse<UserResponse>> {
        return await axios.get(`${this.userUrl}`)
    }
    async update(data: { name: string, email: string, profile: string, skip_api_confirm: boolean }, files: File|null): Promise<AxiosResponse<UpdateProfileResponse>> {
        /**
         * パラメータを設定
         * 画像アップロードとフォームデータを同時送信する場合、下記に注意
         * ・FormDataオブジェクトを使う(ファイルアップロード時の作法)
         */
        let params = new FormData()
        params.append('name', data.name)
        params.append('email', data.email)
        params.append('profile', data.profile)
        // FormData送信のため文字列で送信
        params.append('skip_api_confirm', data.skip_api_confirm ? '1' : '0')
        if (!!files) {
            params.append('file', files)
        }
        return await axios.post(`${this.updateProfileUrl}`, params)
    }
    async passwordUpdate(params: { current_password: string, password: string, password_confirmation: string }): Promise<AxiosResponse<UpdatePasssowrdResponse>> {
        return await axios.put(`${this.updatePasswordUrl}`, params)
    }
    async getPostedStep(): Promise<AxiosResponse<GetPostedStepResponse>> {
        return await axios.get(`${this.getPostedStepUrl}`)
    }
    async getChallengingStep(): Promise<AxiosResponse<GetChallengingStepResponse>> {
        return await axios.get(`${this.getChallengingStepUrl}`)
    }
}

/**
 * APIレスポンスの型指定
 */
export type UserResponse = {
    user: User
}
export type UpdateProfileResponse = {
    user: User
    message: string
}
export type UpdatePasssowrdResponse = {
}
export type GetPostedStepResponse = {
    steps: Step[]
}
export type GetChallengingStepResponse = {
    steps: Step[]
}
