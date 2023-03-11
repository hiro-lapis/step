/**
 * composable(再利用可能な関数)の定義
 * export const で定義し、import { xxx } で指定import
 * @returns
 */
export const useTest = () => {
    const testFunc = () => {
        console.log('test composable')
    }
    return { testFunc }
}
