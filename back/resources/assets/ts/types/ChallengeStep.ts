import { AchievementTimeType } from './AchievementTimeType'
import { Category } from './Category'
import { ChallengeSubStep } from './ChallengeSubStep'

export type ChallengeStep = {
    id: number,
    challenge_user_id: number,
    challenged_at: string,
    cleared_at: string|null,
    status: number,
    status_name?: string,

    // ChallengeStepはサーバーで作成されたデータを表示するためリレーションなどのプロパティも画面表示でよく使う値はnot nullプロパティとして設定
    step_id: number
    post_user_id: number,
    category_id: number
    name: string
    image_url: string
    summary: string
    achievement_time_type_id: number
    challenge_sub_steps: ChallengeSubStep[]
    challenge_sub_steps_count: number
    cleared_sub_step_count: number
    achievement_time_type: AchievementTimeType
    category: Category
    created_at?: string
    // 以下アクセサで必要に応じて設定
    achievement_time_type_name?: string
    category_name?: string
    post_user_name?: string
    post_user_image_url?: string
    post_user_profile?: string
}
