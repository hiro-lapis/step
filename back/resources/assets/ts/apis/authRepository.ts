import axios from './baseRepository'

export class AuthRepository {
  private readonly loginUrl = '/api/login'
  private readonly isLoginUrl = '/api/is-login'
  private readonly logoutUrl = '/api/logout'
  private readonly csrfUrl = '/sanctum/csrf-cookie'

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
}
