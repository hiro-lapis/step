<script setup lang="ts">
import { inject, reactive } from 'vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import { repositoryKey } from '../../../types/common/Injection'

// utility
const $repositories = inject<Repositories>(repositoryKey)!
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()

// data
const forgotData = reactive({
    email: '',
})
// methods
const send = () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.auth.getCsrf()
        .then(() => {
            $repositories.auth.forgotPassword(forgotData)
                .then(() => {
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
            <div class="p-forgot-form">
                <div class="p-forgot-form__container">
                    <div class="p-forgot-form__head">
                        <h2 class="c-title--forgot">パスワードリセット</h2>
                    </div>
                    <div class="p-forgot-form__body">
                        <!-- Eメール -->
                        <div class="p-forgot-form__element">
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
</style>
