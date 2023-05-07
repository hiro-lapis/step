<script setup lang="ts">
import axios from 'axios'
import { useRouter } from 'vue-router'
import { computed, inject, reactive } from 'vue'
import { useUserStore, useMessageInfoStore, useRequestStore } from '../../store/globalStore'
import TextInput from '../Atoms/TextInput.vue'
import BorderLine from '../Atoms/BorderLine.vue'
import { Repositories } from '../../apis/repositoryFactory'
import { repositoryKey } from '../../types/common/Injection'

// utilities
const userStore = useUserStore()
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>(repositoryKey)!
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
</style>
