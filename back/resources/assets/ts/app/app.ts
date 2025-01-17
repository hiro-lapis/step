
// import _ from 'lodash';
// window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import axios from 'axios';

// declare global {
//     interface Window {
//       axios: any;
//     }
// }
// window.axios = axios;
// // SPA認証のためのcredentialを設定
// axios.defaults.withCredentials = true

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

/**
 * ここからapp.js設定
 */

import { createApp } from 'vue'
const app = createApp({})

// router
import { router } from '../routes/routes'
app.use(router)

// pinia
import { createPinia } from 'pinia'
const pinia = createPinia()
app.use(pinia)
// tooltip
import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css'
app.use(FloatingVue)

import { camelCase, upperFirst } from 'lodash'

// ページコンポーネントをグローバル登録
// ./components/Templates配下のコンポーネントをグローバル登録
let requireComponent = require.context('../components/Templates', true, /\w+\.vue$/);
let vueComponentArray: any[] = [];

requireComponent.keys().forEach(fileName => {
    let componentConfig = requireComponent(fileName);
    // コンポーネント名フォーマット
    // ex. ./HelloWorld.vue => HelloWorld
    let componentName = upperFirst(
        camelCase(
            fileName.split('/').pop()!.replace(/\.\w+$/, '')
        )
    );
    // コンポーネント名の重複があればエラーとする
    if ( vueComponentArray.indexOf(componentName) >= 0 ) {
        throw new Error('component duplication error.');
    } else {
        // フォーマットしたコンポーネントファイルを登録
        vueComponentArray.push(componentName);
        app.component(componentName, componentConfig.default || componentConfig);
    }
});

import repository from '../apis/repositoryFactory'
import { conditionKey, repositoryKey } from '../types/common/Injection'
import { Condition, sortType } from '../types/components/Condition'
import { ref } from 'vue'

app.provide(repositoryKey, repository())
app.provide(conditionKey, ref<Condition>({ key_word: '', page: 1, category_id: 0, sort_type: '1', order_by: sortType[1].order_by, desc: sortType[1].desc}))
app.mount("#app")
