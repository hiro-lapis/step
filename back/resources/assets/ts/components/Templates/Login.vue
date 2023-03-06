<script setup lang="ts">
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, reactive } from 'vue'
import { useUserStore, useMessageInfoStore } from '../../store/globalStore'
import { User } from 'assets/ts/types/User'
import TextInput from '../Atoms/TextInput.vue'
import BorderLine from '../Atoms/BorderLine.vue'


// utilities
const userStore = useUserStore()
const messageStore = useMessageInfoStore()

const router = useRouter()

const loginData = reactive({
    email: '',
    password: '',
})
const loading = ref(false)

const login = () => {
    // 多重送信防止
    if (loading.value) return
    loading.value = true
    axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post('/api/login', loginData).then(res => {
            const data: User = res.data
            userStore.setUser(data)
            userStore.setLogin(true)
            router.push({ name: 'themes-list'})
        }).catch(error => {
            // 通信
            if (axios.isAxiosError(error) && error.response?.status === 422) {
                messageStore.setErrorMessage('メールアドレスとパスワードが一致しません')
            }
        })
    })
    loading.value = false
}
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <NotifyMessage />
            <div class="l-container p-container">
                <div class="p-login-form">
                    <div class="p-login-form__container">
                        <div class="p-login-form__head">
                            <h2 class="c-title">ログイン</h2>
                        </div>
                        <div class="p-login-form__body">
                            <!-- SNSでログイン -->
                            <div class="p-login-form__element">
                                <a href="/login/google" class="c-btn c-btn--large c-btn--social-login">
                                    <div class="c-img--icon--sns">
                                        <img class="u-vertical-align--b" src="https://laravel8-haiki-share.s3-ap-northeast-1.amazonaws.com/asset/icon-google.png" alt="googleアイコン">
                                    </div>
                                    <span class="c-btn--social-login__text">Googleアカウントでログイン</span>
                                </a>
                            </div>
                            <BorderLine />
                            <span class="p-login-form__element"></span>
                            <!-- Eメールでログイン -->
                            <div class="p-login-form__element">
                                <TextInput
                                    v-model:value="loginData.email"
                                    type="email"
                                    formId="email"
                                    placeHolder="Eメール"
                                    errorKey="email"
                                />
                            </div>
                            <div class="p-login-form__element">
                                <TextInput
                                    v-model:value="loginData.password"
                                    type="password"
                                    errorKey="password"
                                    placeHolder="パスワード"
                                />
                            </div>
                            <div class="p-login-form__btn-box">
                                <button
                                    @click="login"
                                    class="c-btn--login">
                                    ログイン
                                </button>
                            </div>
                            <div class="p-login-form__text-link">
                                <router-link :to="{ name: 'todo' }">
                                    <span class="c-text-link p-link">
                                        新規登録はこちら
                                    </span>
                                </router-link>
                                <router-link :to="{ name: 'todo' }">
                                    <span class="c-text-link p-link">
                                        パスワードを忘れた方はこちら
                                    </span>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
@import "../../../sass/foundation/_breakpoints.scss";
@import "../../../sass/layout/_container.scss";

.p-container {
    margin-bottom: 50px;
    @include pc() {
        margin-top: 115px;
        margin-bottom: 65px;
    }
}
.p-login-form {
    &__container {
        margin: 0 auto;
        padding: 25px 40px;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
        overflow: hidden;
        @include pc() {
            width: 500px;
            box-shadow: 0 0 8px #ccc;
        }
    }
    &__head {
        margin-bottom: 30px;
    }
    &__body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    &__element {
        width: 100%;
        margin-bottom: 15px;
    }
    &__btn-box {
        margin-bottom: 5px;
        width: 100%;
    }
    &__text-link {
        text-align: left;
    }
}

.p-link {
    margin-bottom: 8px;
    text-align: left;
    display: inline-block;
    width: 100%;
    @include pc() {
        width: 400px;
    }
}
</style>
