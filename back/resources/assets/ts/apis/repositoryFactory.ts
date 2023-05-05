import { AuthRepository } from './authRepository'
import { CommonRepository } from './commonRepository'
import { StepRepository } from './stepRepository'
import { ChallengeStepRepository } from './challengeStepRepository'
import { MyPageRepository } from './myPageRepository'
import { ChatGptRepository } from './chatGptRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository,
  challengeStep: ChallengeStepRepository,
  chatGpt: ChatGptRepository,
  common: CommonRepository,
  mypage: MyPageRepository,
  step: StepRepository,
}

const repositories = {
  auth: new AuthRepository(),
  chatGpt: new ChatGptRepository(),
  common: new CommonRepository(),
  challengeStep: new ChallengeStepRepository(),
  mypage: new MyPageRepository(),
  step: new StepRepository(),
}

export default (): Repositories => repositories
