// コンポーネントをまたいで教諭する検索条件
export type Condition = {
    key_word: string,
    category_id: number,
    page: number,
    sort_type: string,
    order_by: string,
    desc: boolean,
}

/**
 * 検索時に使用する並び替えキー
 */
export const sortType = {
    1: { order_by: 'steps.created_at', desc: true, text: '新着順' },
    2: { order_by: 'sub_steps_count', desc: false, text: 'ステップ数の少ない順' },
    3: { order_by: 'sub_steps_count', desc: true, text: 'ステップ数の多い順' },
    4: { order_by: 'achievement_time_types.sort_number', desc: false, text: '達成時間の短い順' },
    5: { order_by: 'achievement_time_types.sort_number', desc: true, text: '達成時間の長い順' },
}
