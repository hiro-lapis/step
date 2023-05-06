import { InjectionKey, Ref } from 'vue'
import { Condition } from '../components/Condition'
import { Repositories } from '../../apis/repositoryFactory'

// provide,injectで使用するInjectionKeyを定義

/**
 * コンポーネントをまたいで共有する検索条件
 * condition というkeyにCondition型のrefを注入するよう型定義
 */
// export const conditionKey: InjectionKey<Condition> = Symbol('condition')
export const conditionKey: InjectionKey<Ref<Condition>> = Symbol('condition')

/**
 * コンポーネントをまたいで使用するAPIメソッド
 */
export const repositoryKey: InjectionKey<Repositories> = Symbol('$repositories')
