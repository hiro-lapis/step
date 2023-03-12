import { AchievementTimeType } from './AchievementTimeType'
import { Category } from './Category'

export type Step = {
    id?: number|null,
    category_id: number,
    user_id?: number,
    name: string,
    achievement_time_type_id: string,
    achievement_time_type: AchievementTimeType
    category: Category
}
