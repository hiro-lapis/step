import { AxiosResponse } from 'axios'
import { AchievementTimeType } from '../types/AchievementTimeType'
import axios from './baseRepository'

/**
 * マスタデータなど共通データ取得リポジトリ
 */
export class CommonRepository {
    private readonly categoryUrl = '/api/categories'
    private readonly achievementTimeTypeUrl = '/api/achievement-time-types'

    async category(): Promise<any> {
        return await axios.get(`${this.categoryUrl}`)
    }
    async achievementTimeType(): Promise<AxiosResponse<AchievementTimeTypeResponse>> {
        return await axios.get(`${this.achievementTimeTypeUrl}`)
    }
}

type AchievementTimeTypeResponse = AchievementTimeType[]
