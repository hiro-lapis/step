import { createRouter, createWebHistory, RouteLocationNormalized, RouteRecordRaw } from 'vue-router'

import { useUserStore } from '../store/globalStore'
import Top from '../components/Templates/Top.vue'
import Login from '../components/Templates/Login.vue'
import Register from '../components/Templates/Register.vue'
import PasswordForgot from '../components/Templates/Password/Forgot.vue'
import PasswordReset from '../components/Templates/Password/Reset.vue'
import MypageView from '../components/Templates/Mypage/MypageView.vue'
import StepCreateView from '../components/Templates/Steps/StepCreateView.vue'
import StepEditView from '../components/Templates/Steps/StepEditView.vue'
import StepShowView from '../components/Templates/Steps/StepShowView.vue'
import ChallengeStepShowView from '../components/Templates/ChallengeSteps/ChallengeStepShowView.vue'
import StepListView from '../components/Templates/Steps/StepListView.vue'


// ルート登録
const routes: Array<RouteRecordRaw> = [
    { path: '/', name: 'home', component: Top, meta: { title: 'TOPページ', requiresAuth: false, guestOnly: false, }, },
    { path: '/register', name: 'register', component: Register, meta: { title: 'アカウント登録', requiresAuth: false, guestOnly: true, },  },
    { path: '/login', name: 'login', component: Login, meta: { title: 'ログイン', requiresAuth: false, guestOnly: true, },  },
    { path: '/password/forgot', name: 'password-forgot', component: PasswordForgot, meta: { title: 'パスワード再発行', requiresAuth: false, guestOnly: true, } },
    { path: '/password/reset', name: 'password-reset', component: PasswordReset, meta: { title: 'パスワードリセット', requiresAuth: false, guestOnly: true, }, },
    { path: '/steps/create', name: 'steps-create', component: StepCreateView, meta: { title: 'ステップ新規作成', requiresAuth: true, guestOnly: false, },  },
    { path: '/steps/edit/:id', name: 'steps-edit', component: StepEditView, meta: { title: 'ステップ編集', requiresAuth: true, guestOnly: false, },  },
    { path: '/steps', name: 'steps-list', component: StepListView, meta: { title: 'ステップ一覧', requiresAuth: false, guestOnly: false, },   },
    { path: '/steps/:id', name: 'steps-show', component: StepShowView, meta: { title: 'ステップ詳細', requiresAuth: false, guestOnly: false, }, },
    { path: '/challege-steps/:id', name: 'challenge-steps-show', component: ChallengeStepShowView, meta: { title: 'チャレンジ中のステップ詳細', requiresAuth: true, guestOnly: false, }, },
    { path: '/mypage', name: 'mypage', component: MypageView, meta: { title: 'マイページ', requiresAuth: true, guestOnly: false, }, },
]

export const router = createRouter({
    scrollBehavior(to, from, savedPosition) {
        // 履歴を戻る時は保存された位置にスクロール
        if (savedPosition) {
            return savedPosition
        // それ以外はページトップにスクロール
        } else {
            return { top: 0 }
        }
    },
    history: createWebHistory(), // 履歴機能ON
    routes,
})

// 画面遷移、ブラウザリロード時共通処理
router.beforeEach(async (to: RouteLocationNormalized, from, next) => {
    // name 未定義のルートへのアクセスは一律homeへ
    if (to.name === null || to.name === undefined) {
        next({ name: 'home' })
    } else {
        const user = useUserStore()
        // 認証必須のページには
        if (to.meta.requiresAuth && !user.isLogin) next({ name: 'login' })
        else next()
    }
})
