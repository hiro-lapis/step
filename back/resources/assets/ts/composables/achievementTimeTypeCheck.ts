import { AchievementTimeType } from '../types/AchievementTimeType'

/**
 * よく使う類似クラスの型判定処理
 *
 * @returns
 */
export const useAchievementTimeTypeCheck = () => {
    const Ids = { 1: 'minute', 2: 'hour', 3: 'day', 4: 'week', 5: 'month', 6: 'year' }
    // 達成目安時間の単位に応じた設定可能最大値を返す
    const getLimitTimeCount = (achievementTimeTypeId: number): number => {
        switch (Ids[achievementTimeTypeId]) {
            case 'minute':
                return 59
            case 'hour':
                return 23
            case 'day':
                return 30
            case 'week':
                return 4
            case 'month':
                return 11
            case 'year':
                return 99
            default:
                return 0
        }
    }
    const getValidTimeCountSpan = (achievementTimeTypeId: number): string => {
        switch (Ids[achievementTimeTypeId]) {
            case 'minute':
                return '1~59分'
            case 'hour':
                return '1~23時間'
            case 'day':
                return '1~30日'
            case 'week':
                return '1~4週間'
            case 'month':
                return '1~11ヶ月'
            case 'year':
                return '1~99年'
            default:
                return ''
        }
    }
    return { getLimitTimeCount, getValidTimeCountSpan }
}
