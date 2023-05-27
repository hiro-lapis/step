<script setup lang="ts">
import { computed, inject, reactive, ref, watch } from 'vue'
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
const errorMessage = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
})
const currentPasswordRef = ref<InstanceType<typeof TextInput>>()
const passwordRef = ref<InstanceType<typeof TextInput>>()
const passwordConfirmationRef = ref<InstanceType<typeof TextInput>>()
// computed
const isInvalid = computed(() => {
    return errorMessage.current_password !== ''
        || errorMessage.password !== ''
        || errorMessage.password_confirmation !== ''
})
// methods
const validate = useValidation()
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
    // エラーメッセージ初期化
    errorMessage.current_password = ''
    errorMessage.password = ''
    errorMessage.password_confirmation = ''
    // input の パスワード表示を隠す
    currentPasswordRef.value?.hidePassword()
    passwordRef.value?.hidePassword()
    passwordConfirmationRef.value?.hidePassword()
}
// watch
// reactive passwordDataを監視
watch([() => passwordData.current_password, ],
    ([ newCurrentPassword ], []) => {
        let result: true | string
        result = validate.password(newCurrentPassword)
        errorMessage.current_password = result === true ? '' : result
    }
)
watch([() => passwordData.password, ],
    ([ newPassword ], []) => {
        let result: true | string
        result = validate.password(newPassword)
        errorMessage.password = result === true ? '' : result
        result = validate.passwordConfirmation(newPassword, passwordData.password_confirmation)
        if (errorMessage.password_confirmation !== '') {
            // パスワード確認用の入力がある場合
            errorMessage.password_confirmation = result === true ? '' : result
        }
    }
)
watch(
    [ () => passwordData.password_confirmation, ],
    ([ newPasswordConfirmation ], []) => {
        let result: true | string
        result = validate.passwordConfirmation(passwordData.password, newPasswordConfirmation)
        errorMessage.password_confirmation = result === true ? '' : result
    }
)
</script>

<template>
    <Transition>
        <aside class="c-password-modal__container">
            <div @click="handleClose" class="c-password-modal__bg">
                <div @click="stopClose" class="c-password-modal__form">
                    <!-- 総合評価ボタン -->
                    <h3 class="c-password-modal__title">{{ title }}</h3>
                        <div class="c-password-modal__element">
                            <!-- autocomplateで自動入力されることあり -->
                            <TextInput
                                v-model:value="passwordData.current_password"
                                :errorMessage="errorMessage.current_password"
                                ref="currentPasswordRef"
                                placeHolder="現在のパスワード"
                                type="password"
                            />
                        </div>
                    <div class="c-password-modal__element">
                        <TextInput
                            v-model:value="passwordData.password"
                            :errorMessage="errorMessage.password"
                            ref="passwordRef"
                            type="password"
                            placeHolder="パスワード(半角英字8~24文字)"
                        />
                    </div>
                    <div class="c-password-modal__element">
                        <TextInput
                            v-model:value="passwordData.password_confirmation"
                            :errorMessage="errorMessage.password_confirmation"
                            ref="passwordConfirmationRef"
                            errorKey="password_confirmation"
                            type="password"
                            placeHolder="パスワード(確認用)"
                        />
                    </div>
                    <div class="c-password-modal__btn-box">
                        <button
                            @click="updatePassword"
                            :disabled="isInvalid"
                            class="c-btn--exec-update-password">
                            送信
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </Transition>
</template>

<style lang="scss" scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
