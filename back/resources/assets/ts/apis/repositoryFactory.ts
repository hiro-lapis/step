import { AuthRepository } from './authRepository'
import { CommonRepository } from './commonRepository'
import { StepRepository } from './stepRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository,
  common: CommonRepository,
  step: StepRepository,
}

const repositories = {
  auth: new AuthRepository(),
  common: new CommonRepository(),
  step: new StepRepository(),
}

export default (): Repositories => repositories
