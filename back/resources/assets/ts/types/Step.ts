import { AchievementTimeType } from './AchievementTimeType'
import { Category } from './Category'
import { SubStep } from './SubStep'
import { User } from './User'

export type Step = {
    id?: number|null,
    category_id: number
    user_id?: number
    name: string
    sub_steps: SubStep[]
    achievement_time_type_id: number
    achievement_time_type: AchievementTimeType
    category: Category
    created_at?: string
    // 以下アクセサで必要に応じて設定
    user_name?: string
    user_image_url?: string
    user_profile?: string
}
