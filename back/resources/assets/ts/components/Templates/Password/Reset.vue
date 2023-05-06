<script setup lang="ts">
import { inject, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Repositories } from '../../../apis/repositoryFactory'
import { useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import { repositoryKey } from '../../../types/common/Injection'

// utility
const route = useRoute()
const router = useRouter()
const $repositories = inject<Repositories>(repositoryKey)!
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()

// props

// data
const resetData = reactive({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
})

// method
const update = () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.auth.getCsrf()
        .then(() => {
            $repositories.auth
                .forgotReset({
                    email: resetData.email,
                    token: resetData.token,
                    password: resetData.password,
                    password_confirmation: resetData.password_confirmation
                })
                .then(() => {
                    messageStore.setMessage('パスワードを変更しました')
                    // ログインページへ遷移
                    router.push({ name: 'login' })
                })
                .finally(() => {
                    requestStore.setLoading(false)
                })
        })
}
onMounted(() => {
    // クエリパラメータのtokenをリセット情報にセット
    if (route.query.token && typeof route.query.token === 'string') {
        resetData.token = route.query.token;
    }

    if (route.query.email) {
        resetData.email = route.query.email as string;
    }
})
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <div class="p-form">
                <div class="p-form__container">
                    <div class="p-form__head">
                        <h2>パスワードリセット</h2>
                    </div>

                    <div class="p-form__body">
                        <!-- Eメール -->
                        <div class="p-form__element">
                            <TextInput
                                v-model:value="resetData.email"
                                errorKey="email"
                                type="email"
                                formId="email"
                                placeHolder="Eメールアドレス"
                            />
                        </div>
                        <div class="p-form__element">
                            <TextInput
                                v-model:value="resetData.password"
                                errorKey="password"
                                type="password"
                                formId="password"
                                placeHolder="新パスワード(8~16文字)"
                            />
                        </div>
                        <div class="p-form__element">
                            <TextInput
                                v-model:value="resetData.password_confirmation"
                                @keyupEnter="update"
                                errorKey="password_confirmation"
                                type="password"
                                formId="password-confirm"
                                placeHolder="新パスワード(確認用)"
                            />
                        </div>
                        <button
                            class="c-btn--reset"
                            @click="update"
                        >
                            リセット
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
@import "../../../../sass/foundation/breakpoints";
@import "../../../../sass/layout/_container.scss";

.p-container {
    margin-top: 130px;
    margin-bottom: 50px;
    @include pc() {
        margin-bottom: 65px;
    }
}

.p-form {
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
            // border: 1px solid #d6d6d6; // くっきりラインタイプ
        }
    }
    &__head {
        margin-bottom: 15px;
        text-align: center;
        font-size: 16px;
    }
    &__body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    &__element {
        width: 100%;
        margin-bottom: 10px;
        &:last-of-type {
            margin-bottom: 15px;
        }
    }
}
</style>
