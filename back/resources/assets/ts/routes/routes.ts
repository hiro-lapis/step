import { createRouter, createWebHistory } from 'vue-router'

import HelloWorld from '../components/Templates/HelloWorld.vue'
import GoodMorning from '../components/Templates/GoodMorning.vue'

// ルート登録
const routes = [
    { path: '/hello', component: HelloWorld },
    { path: '/good-morning', component: GoodMorning }
]

export const router = createRouter({
    history: createWebHistory(), // 履歴機能ON
    routes,
})
