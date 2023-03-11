import { AuthRepository } from './authRepository'
import { CommonRepository } from './commonRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository,
  common: CommonRepository
}

const repositories = {
  auth: new AuthRepository(),
  common: new CommonRepository(),
}

export default (): Repositories => repositories
