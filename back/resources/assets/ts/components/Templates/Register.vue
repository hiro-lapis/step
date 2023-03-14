<script setup lang="ts">
import { useRouter } from 'vue-router'
import { computed, inject, reactive, ref } from 'vue'
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
    passwordConfirmation: '',
})

// computed
const loading = computed(() => requestStore.isLoading)

// watch

// methods
const register = async () => {
    if (loading.value) return
    requestStore.setLoading(true)
    // パラメータのセット
    const params = convertKeysToSnakeCase(registerData)

    $repositories.auth.register(params as RegisterData)
        .then(response => {
            requestStore.setLoading(false)
            userStore.setUser(response.data.user)
            userStore.setLogin(true)

            // インデックス画面へ遷移
            messageStore.setMessage('登録ありがとうございます！テーマ一覧へ遷移します。')
            router.push({ name: 'themes-list' })
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}

const toSnakeCase = (str: string) => {
  return str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
}

const convertKeysToSnakeCase = (obj: Object) => {
  const snakeCaseObj = {};
  for (let key in obj) {
    if (obj.hasOwnProperty(key)) {
      const snakeCaseKey = toSnakeCase(key);
      snakeCaseObj[snakeCaseKey] = obj[key];
    }
  }
  return snakeCaseObj;
}
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <div class="p-register-form">
                <div class="p-register-form__container">
                    <div class="p-register-form__head">
                        <h2 class="c-title">アカウント登録</h2>
                    </div>
                    <div class="p-register-form__body">
                        <!-- SNSで登録 -->
                        <div class="p-register-form__element">
                            <a href="/login/google" class="c-btn c-btn--large c-btn--social-login">
                                <div class="c-img--icon--sns">
                                    <img class="u-vertical-align--b" src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/icon-google.png" alt="googleアイコン">
                                </div>
                                <span class="c-btn--social-login__text">Googleアカウントで登録</span>
                            </a>
                        </div>
                        <border-line />
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
                                placeHolder="パスワード"
                            />
                        </div>
                        <div class="p-register-form__element">
                            <TextInput
                                type="password"
                                v-model:value="registerData.passwordConfirmation"
                                @keyupEnter="register"
                                errorKey="password_confirmation"
                                placeHolder="パスワード(確認用)"
                            />
                        </div>
                        <button
                            class="c-btn--register"
                            @click="register"
                        >
                            アカウント登録
                        </button>
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
.p-register-form {
    &__container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 auto;
        padding: 25px 40px;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
        overflow: hidden;
        @include pc() {
            width: 500px;
            box-shadow: 0 0 8px #ccc;
            // border: 1px solid #d6d6d6; // くっきりラインタイプ
        }
    }
    &__head {
        margin-bottom: 25px;
    }
    &__body {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        @include pc() {
            width: 400px;
        }
    }
    &__element {
        width: 100%;
        margin-bottom: 15px;
        &:nth-of-type(6) {
            margin-bottom: 10px;
        }
    }
}
</style>
