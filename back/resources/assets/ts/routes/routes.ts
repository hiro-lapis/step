import { createRouter, createWebHistory, RouteLocationNormalized } from 'vue-router'

import { useUserStore } from '../store/globalStore'
import HelloWorld from '../components/Templates/HelloWorld.vue'
import GoodMorning from '../components/Templates/GoodMorning.vue'
import Login from '../components/Templates/Login.vue'
import Register from '../components/Templates/Register.vue'
import PasswordForgot from '../components/Templates/Password/Forgot.vue'
import PasswordReset from '../components/Templates/Password/Reset.vue'
import ThemeListView from '../components/Templates/Themes/ListView.vue'
import StepEditView from '../components/Templates/Steps/StepEditView.vue'
import StepShowView from '../components/Templates/Steps/StepShowView.vue'
import StepListView from '../components/Templates/Steps/StepListView.vue'

// ルート登録
const routes = [
    { path: '/', name: 'home', component: HelloWorld, },
    { path: '/todo', name: 'todo', component: GoodMorning }, // 未作成画面の仮リンク先
    { path: '/register', name: 'register', component: Register }, // 未作成画面の仮リンク先
    { path: '/good-morning', name: 'good-morning', component: GoodMorning },
    { path: '/login', name: 'login', component: Login },
    { path: '/themes', name: 'themes-list', component: ThemeListView, },
    { path: '/password/forgot', name: 'password-forgot', component: PasswordForgot, },
    { path: '/password/reset', name: 'password-reset', component: PasswordReset, },
    { path: '/steps/create', name: 'steps-create', component: StepEditView, },
    { path: '/steps', name: 'steps-list', component: StepListView, },
    { path: '/steps/:id', name: 'steps-show', component: StepShowView, },
]


export const router = createRouter({
    history: createWebHistory(), // 履歴機能ON
    routes,
})

// 未ログイン状態で表示できるページ
export const guestPageName = [
    'register',
    'login',
    'password-forgot',
    'password-reset',
    'steps-list',
    'steps-show'
]

// 未ログイン状態の時のみ表示するぺーこ
export const guestOnlyPageName = [
    'register',
    'login',
    'password-forgot',
    'password-reset',
]

// 画面遷移、ブラウザリロード時共通処理
// 認証が必要なページを開いているときはログインページへリダイレクト
router.beforeEach(async (to: RouteLocationNormalized, from, next) => {
    // ルート定義時はnameを必ず指定すること
    const loginRequired = !guestPageName.includes(to.name!.toString())
    const user = useUserStore()
    if (loginRequired && !user.isLogin) next({ name: 'login' })
    else next()
})
