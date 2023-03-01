// editor,IDEでtsファイル内で.vueファイルをimportする時にファイル情報を提供する設定ファイル
declare module "*.vue" {
    import type { DefineComponent } from "vue"
    const component: DefineComponent<{}, {}, any>
    export default component
}
