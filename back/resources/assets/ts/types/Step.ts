import { AchievementTimeType } from './AchievementTimeType'
import { Category } from './Category'
import { SubStep } from './SubStep'
import { User } from './User'

export type Step = {
    id?: number,
    category_id: number
    user_id?: number
    name: string
    summary: string
    image_url: string
    sub_steps: SubStep[]
    achievement_time_type_id: number
    time_count: number
    is_active?: boolean
    // draft: {}
    sub_steps_count?: number
    achievement_time_type?: AchievementTimeType
    category?: Category
    is_writer?: boolean
    created_at?: string
    // 以下アクセサで必要に応じて設定
    achievement_time?: string
    is_cleared?: boolean
    is_challenged?: boolean
    category_name?: string
    user_name?: string
    user_image_url?: string
    user_profile?: string
}
