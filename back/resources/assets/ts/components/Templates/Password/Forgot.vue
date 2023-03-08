<script setup lang="ts">
import { inject, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { Repositories } from '../../../apis/repositoryFactory'
import { useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'

// utility
const $repositories = inject<Repositories>('$repositories')!
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()

// data
const forgotData = reactive({
    email: '',
})

const send = () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.auth.getCsrf()
        .then(() => {
            $repositories.auth.forgotPassword(forgotData)
                .then((response) => {
                    messageStore.setMessage('パスワードリセットメールを送信しました。<br>確認の上パスワード再発行の手続きを進めてください。')
                }).finally(() => {
                    requestStore.setLoading(false)
                })
        })
}
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
                                v-model:value="forgotData.email"
                                @keyupEnter="send"
                                errorKey="email"
                                type="email"
                                formId="email"
                                placeHolder="メールアドレス"
                            />
                        </div>
                        <button
                            class="c-btn--forgot"
                            @click="send"
                        >
                            送信
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/layout/_container.scss";

.p-container {
    margin-top: 150px;
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
        margin-bottom: 30px;
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
        margin-bottom: 30px;
    }
}
</style>
