<script setup lang="ts">
import axios from 'axios'
import { useRouter } from 'vue-router'
import { computed, inject, reactive } from 'vue'
import { useUserStore, useMessageInfoStore, useRequestStore } from '../../store/globalStore'
import TextInput from '../Atoms/TextInput.vue'
import BorderLine from '../Atoms/BorderLine.vue'
import { Repositories } from '../../apis/repositoryFactory'

// utilities
const userStore = useUserStore()
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>('$repositories')!
const router = useRouter()

// data
const loginData = reactive({
    email: '',
    password: '',
})

// computed
const loading = computed(() => {
    return requestStore.isLoading
})
// methods
const login = () => {
    // 多重送信防止
    if (loading.value) return
    requestStore.setLoading(true)
    $repositories.auth.getCsrf().then(() => {
        $repositories.auth.login(loginData)
            .then(res => {
                requestStore.setLoading(false)
                userStore.setUser(res.data)
                userStore.setLogin(true)
                router.push({ name: 'steps-list'})
            }).catch(error => {
                requestStore.setLoading(false)
                if (axios.isAxiosError(error) && error.response?.status === 422) {
                    messageStore.setErrorMessage('メールアドレスとパスワードが一致しません')
                }
            })
    })
}
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <div class="p-login-form">
                <div class="p-login-form__container">
                    <div class="p-login-form__head">
                        <h2 class="c-title--login">ログイン</h2>
                    </div>
                    <div class="p-login-form__body">
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
                                @keyup-enter="login"
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
                        <div class="p-login-form__element">
                            <BorderLine />
                        </div>
                        <div class="p-login-form__text-link">
                            <router-link :to="{ name: 'register' }">
                                <span class="c-text-link p-link">
                                    新規登録はこちら
                                </span>
                            </router-link>
                            <router-link :to="{ name: 'password-forgot' }">
                                <span class="c-text-link p-link">
                                    パスワードを忘れた方はこちら
                                </span>
                            </router-link>
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
        margin-top: 115px; // ヘッダーとの余白
        margin-bottom: 205px; // フッターとの余白
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
        @include mq() {
            width: 500px;
            box-shadow: 0 0 8px #ccc;
        }
    }
    &__head {
        margin-bottom: 10px;
        @include pc() {
            margin-bottom: 30px;
        }
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
        margin-bottom: 20px;
        width: 100%;
    }
    &__text-link {
        text-align: left;
        width: 100%;
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
