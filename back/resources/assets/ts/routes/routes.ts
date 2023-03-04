import { createRouter, createWebHistory } from 'vue-router'

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
    { path: '/themes', name: 'themes-list', component: ThemeListView }, // テーマ一覧
]

export const router = createRouter({
    history: createWebHistory(), // 履歴機能ON
    routes,
})
