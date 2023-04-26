type CategoryColor = {
    id: number,
    name: string,
    rgba: string, // 半透明色
    hex: string,
}
export const CategoryColorList = [
    { id: 1, name: 'プログラミング', rgba: 'rgb(244 143 177 / 50%)', hex: '#f48fb1' },
    { id: 2, name: '英語', rgba: 'rgb(128 203 196 / 50%)', hex: '#80cbc4' },
    { id: 3, name: 'ダイエット', rgba: 'rgb(22 165 222 / 50%)', hex: '#16A5DE' },
    { id: 4, name: '朝活', rgba: 'rgb(135 192 65 / 50%)', hex: '#87C041' },
] as const
