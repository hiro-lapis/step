import { RouterLocation } from '../common/Router'

export type Menu = {
    name: string,
    icon?: string,
    to: RouterLocation,
    // to: { name: string, params?: {} }, // 遷移先ルート
}
