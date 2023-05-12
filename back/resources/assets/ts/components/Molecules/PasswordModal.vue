<script setup lang="ts">
import { inject, reactive, watch } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import { useMessageInfoStore, useRequestStore } from '../../store/globalStore'
import TextInput from '../Atoms/TextInput.vue'
import { repositoryKey } from '../../types/common/Injection'
import { useValidation } from '../../composables/validation'
// utilities
const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const $repositories = inject<Repositories>(repositoryKey)!
// props
defineProps({
    title: { required: true, type: String, },
    passwordExists: { required: false, type: Boolean, default: true }, // パスワード設定済ユーザーか
})
// emits
interface Emits {
    (e: 'close'): void
}
const emit = defineEmits<Emits>()
// data
const passwordData = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
})
const errorMessages = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
})

// methods
const rules = useValidation()
const updatePassword = async () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.mypage.passwordUpdate(passwordData)
        .then(() => {
            // fortify APIのためメッセージはフロント側でセット
            messageStore.setMessage('パスワードを変更しました')
            reset()
            // モーダルを閉じるイベント発行
            emit('close')
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
const handleClose = () => emit('close')

const stopClose = (event: Event) => {
    event.stopPropagation()
}
const reset = () => {
    passwordData.current_password = ''
    passwordData.password = ''
    passwordData.password_confirmation = ''
    errorMessages.current_password = ''
    errorMessages.password = ''
    errorMessages.password_confirmation = ''
}
// watch
// TODO: 対応
watch(
    // reactive passwordDataを監視
    [
        () => passwordData.current_password,
        () => passwordData.password,
        () => passwordData.password_confirmation,
    ],
    ([ newCurrentPassword, newPassword, newPasswordConfirmation ], []) => {
        // console.log(' cp>' + newCurrentPassword)
        // console.log(' p>' + newPassword)
        // console.log(' pc>' + newPasswordConfirmation)
        // validate()
    }
)
// const validate = () => {
//     let message: string|true
//     message = rules.required(passwordData.current_password)
//     errorMessages.current_password = message === true ? '' : message
//     message = rules.password(passwordData.password)
//     errorMessages.password = message === true ? '' : message
//     message = rules.required(passwordData.password_confirmation)
//     errorMessages.password_confirmation = message === true ? '' : message
//     return (
//         errorMessages.current_password === '' &&
//         errorMessages.password === '' &&
//         errorMessages.password_confirmation === ''
//     )
// }
</script>

<template>
    <transition>
        <aside class="c-password-modal__container">
            <div @click="handleClose" class="c-password-modal__bg">
                <div @click="stopClose" class="c-password-modal__form">
                    <!-- 総合評価ボタン -->
                    <h3 class="c-password-modal__title">{{ title }}</h3>
                        <div class="c-password-modal__element">
                            <!-- autocomplateで自動入力されることあり -->
                            <TextInput
                                v-model:value="passwordData.current_password"
                                errorKey="current_password"
                                placeHolder="現在のパスワード"
                                type="password"
                            />
                        </div>
                    <div class="c-password-modal__element">
                        <TextInput
                            v-model:value="passwordData.password"
                            :rules="[rules.password]"
                            type="password"
                            placeHolder="パスワード(半角英字8~24文字)"
                        />
                    </div>
                    <div class="c-password-modal__element">
                        <TextInput
                            v-model:value="passwordData.password_confirmation"
                            errorKey="password_confirmation"
                            type="password"
                            placeHolder="パスワード(確認用)"
                        />
                    </div>
                    <div class="c-password-modal__btn-box">
                        <button
                            @click="updatePassword"
                            class="c-btn--exec-update-password">
                            送信
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </transition>
</template>

<style lang="scss" scoped>
@import "../../../sass/foundation/_breakpoints.scss";

.v-enter,
.v-leave-to {
    opacity: 0;
}
.v-enter-to,
.v-leave {
    opacity: 1;
}
.v-enter-active {
    transition: opacity 400ms;
}
.v-leave-active {
    transition: opacity 600ms;
}
</style>
