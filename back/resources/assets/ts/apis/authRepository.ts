import { AxiosResponse } from 'axios'
import { User } from '../types/User'
import axios from './baseRepository'

export class AuthRepository {
    private readonly csrfUrl = '/sanctum/csrf-cookie'
    private readonly registerUrl = '/api/register'
    private readonly isLoginUrl = '/api/is-login'
    private readonly loginUrl = '/api/login'
    private readonly logoutUrl = '/api/logout'
    private readonly forgotPasswordUrl = '/api/forgot-password'
    private readonly resetPasswordUrl = '/api/reset-password'

    async getCsrf(): Promise<any> {
        return await axios.get(`${this.csrfUrl}`)
    }
    async register(params: { name: string, email: string, password: string, password_confirmation: string}): Promise<any> {
        return await axios.post(`${this.registerUrl}`, params)
    }
    async isLogin(): Promise<AxiosResponse<IsLoginResponse>> {
        return await axios.get(`${this.isLoginUrl}`)
    }
    async login(params: {email: string, password: string}): Promise<AxiosResponse<LoginResponse>> {
        return await axios.post(`${this.loginUrl}`, params)
    }
    async logout(): Promise<any> {
        return await axios.post(this.logoutUrl)
    }
    async forgotPassword(params: { email: string }): Promise<any> {
        return await axios.post(this.forgotPasswordUrl, params)
    }
    async forgotReset(params: { email: string, token: string, password: string, password_confirmation: string }): Promise<any> {
        return await axios.post(this.resetPasswordUrl, params)
    }
}

type IsLoginResponse = {
    is_login: boolean,
    user?: { id: number, name: string, email: string, profile: string, image_url: string, skip_api_confirm: boolean, },
    step_ids: number[],
    remain_count: number,
}

type LoginResponse = User
