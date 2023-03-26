import { AuthRepository } from './authRepository'
import { CommonRepository } from './commonRepository'
import { StepRepository } from './stepRepository'
import { MyPageRepository } from './myPageRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository,
  common: CommonRepository,
  mypage: MyPageRepository,
  step: StepRepository,
}

const repositories = {
  auth: new AuthRepository(),
  common: new CommonRepository(),
  mypage: new MyPageRepository(),
  step: new StepRepository(),
}

export default (): Repositories => repositories
