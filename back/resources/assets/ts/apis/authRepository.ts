import axios from './baseRepository'

export class AuthRepository {
    private readonly loginUrl = '/api/login'
    private readonly isLoginUrl = '/api/is-login'
    private readonly logoutUrl = '/api/logout'
    private readonly csrfUrl = '/sanctum/csrf-cookie'
    private readonly forgotPasswordUrl = '/api/forgot-password'
    private readonly resetPasswordUrl = '/api/reset-password'

    async getCsrf(): Promise<any> {
        return await axios.get(`${this.csrfUrl}`)
    }
    async isLogin(): Promise<any> {
        return await axios.get(`${this.isLoginUrl}`)
    }
    async login(data: {email: string, password: string}): Promise<any> {
        return await axios.post(`${this.loginUrl}`, data)
    }
    async logout(): Promise<any> {
        return await axios.post(this.logoutUrl)
    }
    async forgotPassword(data: { email: string }): Promise<any> {
        return await axios.post(this.forgotPasswordUrl, data)
    }
    async forgotReset(data: { email: string, token: string, password: string, password_confirmation: string }): Promise<any> {
        return await axios.post(this.resetPasswordUrl, data)
    }
}
