import axios from './baseRepository'

/**
 * マスタデータなど共通データ取得リポジトリ
 */
export class CommonRepository {
    private readonly categoryUrl = '/api/categories'

    async category(): Promise<any> {
        return await axios.get(`${this.categoryUrl}`)
    }
}
