import { AuthRepository } from './authRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository
}

const repositories = {
  auth: new AuthRepository(),
}

export default (): Repositories => repositories
