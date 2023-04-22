export const ChallengeStatus = {
    'NotChallenged': 0, // 未挑戦
    'Challenging': 1, // 挑戦中
    'Retry': 2,   // 再挑戦
    'Lating': 3, // 挑戦中で達成期限を過ぎている
    'Cleared': 10,   // 達成
    'Lated': 20,  // 達成はしたが達成期限を過ぎていた
    'Failed': 30,  // 失敗
}

export function ChallengeStatusJudgement() {
    const isInChallenge = (status: number) => {
        return status === ChallengeStatus.Challenging
            || status === ChallengeStatus.Retry
            || status === ChallengeStatus.Lating
    }
    const isCleard = (status: number) => {
        return status === ChallengeStatus.Cleared
            || status === ChallengeStatus.Lated
    }
    const isFailed = (status: number) => {
        return status === ChallengeStatus.Failed
    }
    return { isInChallenge, isCleard, isFailed }
}
