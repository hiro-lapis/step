export type ChallengeSubStep = {
    id: number,
    challenge_step_id: number,
    challenged_at: string,
    cleared_at: string|null,
    status: number,

    // 以下アクセサで必要に応じて設定
    status_name?: string,
    sub_step_id: number
    name: string
    created_at?: string
}
