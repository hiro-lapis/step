import { AuthRepository } from './authRepository'
import { CommonRepository } from './commonRepository'
import { StepRepository } from './stepRepository'
import { ChallengeStepRepository } from './challengeStepRepository'
import { MyPageRepository } from './myPageRepository'
import { ChatGptRepository } from './chatGptRepository'
import { FileManageRepository } from './uploadRepository'

export interface Repositories {
  // リソース増加で随時追加
  auth: AuthRepository,
  challengeStep: ChallengeStepRepository,
  chatGpt: ChatGptRepository,
  common: CommonRepository,
  mypage: MyPageRepository,
  step: StepRepository,
  file: FileManageRepository,
}

const repositories = {
  auth: new AuthRepository(),
  chatGpt: new ChatGptRepository(),
  common: new CommonRepository(),
  challengeStep: new ChallengeStepRepository(),
  mypage: new MyPageRepository(),
  step: new StepRepository(),
  file: new FileManageRepository(),
}

export default (): Repositories => repositories
