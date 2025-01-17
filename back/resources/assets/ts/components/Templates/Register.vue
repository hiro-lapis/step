<script setup lang="ts">
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
type RegisterData = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
}
const registerData = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

// computed
const loading = computed(() => requestStore.isLoading)

// methods
const register = async () => {
    if (loading.value) return
    requestStore.setLoading(true)
    // パラメータのセット
    const params = registerData

    $repositories.auth.register(params as RegisterData)
        .then(response => {
            requestStore.setLoading(false)
            userStore.setUser(response.data.user)
            userStore.setLogin(true)
            router.push({ name: 'steps-list' })
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <div class="p-register-form">
                <div class="p-register-form__container">
                    <div class="p-register-form__head">
                        <h2 class="c-title--register">アカウント登録</h2>
                    </div>
                    <div class="p-register-form__body">
                        <div class="p-register-form__element"></div>

                        <!-- Eメールとパスワードで登録 -->
                        <div class="p-register-form__element">
                            <TextInput
                                v-model:value="registerData.name"
                                formId="name"
                                placeHolder="アカウント名"
                                errorKey="name"
                            />
                        </div>
                        <div class="p-register-form__element">
                            <TextInput
                                type="email"
                                v-model:value="registerData.email"
                                formId="email"
                                placeHolder="Eメール"
                                errorKey="email"
                            />
                        </div>
                        <div class="p-register-form__element">
                            <TextInput
                                type="password"
                                v-model:value="registerData.password"
                                errorKey="password"
                                placeHolder="パスワード(8~24字の半角英字)"
                            />
                        </div>
                        <div class="p-register-form__element">
                            <TextInput
                                type="password"
                                v-model:value="registerData.password_confirmation"
                                @keyupEnter="register"
                                errorKey="password_confirmation"
                                placeHolder="パスワード(確認用)"
                            />
                        </div>
                        <div class="p-register-form__element">
                            <button
                                class="c-btn--register"
                                @click="register"
                            >
                                アカウント登録
                            </button>
                        </div>
                        <div class="p-register-form__element">
                            <BorderLine />
                        </div>
                        <div class="p-register-form__text-link">
                            <router-link :to="{ name: 'login' }">
                                <span class="c-text-link p-link">
                                    アカウントをお持ちの方はこちら
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
