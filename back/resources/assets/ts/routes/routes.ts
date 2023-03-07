import { createRouter, createWebHistory, RouteLocationNormalized } from 'vue-router'

import { useUserStore } from '../store/globalStore'
import HelloWorld from '../components/Templates/HelloWorld.vue'
import GoodMorning from '../components/Templates/GoodMorning.vue'
import Login from '../components/Templates/Login.vue'
import ThemeListView from '../components/Templates/Themes/ListView.vue'

// ルート登録
const routes = [
    { path: '/', name: 'home', component: HelloWorld, },
    { path: '/todo', name: 'todo', component: GoodMorning }, // 未作成画面の仮リンク先
    { path: '/good-morning', name: 'good-morning', component: GoodMorning },
    { path: '/login', name: 'login', component: Login },
    { path: '/themes', name: 'themes-list', component: ThemeListView, },
]


export const router = createRouter({
    history: createWebHistory(), // 履歴機能ON
    routes,
})
export const guestPageUrl = ['/login']

// 画面遷移、ブラウザリロード時共通処理
// 認証が必要なページを開いているときはログインページへリダイレクト
router.beforeEach(async (to: RouteLocationNormalized, from, next) => {
    const loginRequired = !guestPageUrl.includes(to.path)
    const user = useUserStore()
    if (loginRequired && !user.isLogin) next({ name: 'login' })
    else next()
})
