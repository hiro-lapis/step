/**
 * 文字列変換関数
 *
 * @returns
 */
export const useStringConverter = () => {
    // snake case への変換
    const toSnakeCase = (str: string) => {
        return str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
    }
    // objectのkeyをsnake case へ変換
    const convertKeysToSnakeCase = (obj: Object) => {
        const snakeCaseObj = {};
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
            const snakeCaseKey = toSnakeCase(key);
            snakeCaseObj[snakeCaseKey] = obj[key];
            }
        }
        return snakeCaseObj;
        }
    return { convertKeysToSnakeCase }
}
