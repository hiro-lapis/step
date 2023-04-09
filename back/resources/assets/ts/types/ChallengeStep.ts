import { AchievementTimeType } from './AchievementTimeType'
import { Category } from './Category'
import { SubStep } from './SubStep'
import { User } from './User'

export type ChallengeStep = {
    id: number,
    challenge_user_id: number,
    challenged_at: string,
    cleared_at: string|null,
    status: number,
    status_name?: string,

    //  ここからオリジナルのステップ情報
    step_id: number
    post_user_id: number,
    category_id: number
    name: string
    sub_steps: SubStep[]
    achievement_time_type_id: number
    achievement_time_type: AchievementTimeType
    category: Category
    created_at?: string
    // 以下アクセサで必要に応じて設定
    category_name?: string
    post_user_name?: string
    post_user_image_url?: string
    post_user_profile?: string
}
