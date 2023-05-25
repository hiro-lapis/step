import { Step } from '../types/Step'
import { SubStep } from '../types/SubStep'
import { ChallengeStep } from '../types/ChallengeStep'
import { ChallengeSubStep } from '../types/ChallengeSubStep'

/**
 * よく使う類似クラスの型判定処理
 *
 * @returns
 */
export const useTypeGuards = () => {
    // is assertion 単なるbool値でなく型アサーションを戻り値として返す
    const isChallengeStep = (step: Step|ChallengeStep): step is ChallengeStep => {
        return (step as ChallengeStep).challenge_user_id !== undefined
    }
    const isChallengeSubStep = (subStep: SubStep|ChallengeSubStep): subStep is ChallengeSubStep => {
        return (subStep as ChallengeSubStep).status !== undefined
    }
    const isDraftStep = (step: Step|ChallengeStep): boolean => {
        if (isChallengeStep(step)) return false
        return !step.is_active ?? false
    }

    return { isChallengeStep, isChallengeSubStep, isDraftStep }
}
