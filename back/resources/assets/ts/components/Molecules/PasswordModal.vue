<script setup lang="ts">
import { inject, reactive } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import { useMessageInfoStore, useRequestStore } from '../../store/globalStore'
import TextInput from '../Atoms/TextInput.vue'

const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const $repositories = inject<Repositories>('$repositories')!

// props
const props = defineProps({
    title: { required: true, type: String, },
    passwordExists: { required: false, type: Boolean, default: true }, // パスワード設定済ユーザーか
})

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

// methods
const updatePassword = async () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    $repositories.mypage.passwordUpdate(passwordData)
        .then(response => {
            // fortify APIのためメッセージはフロント側でセット
            messageStore.setMessage('パスワードを変更しました')
            reset()
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}
const handleClose = () => {
    emit('close')
}
const stopClose = (event: Event) => {
    event.stopPropagation()
}
const reset = () => {
    passwordData.current_password = ''
    passwordData.password = ''
    passwordData.password_confirmation = ''
}
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
                                placeHolder="現在のパスワード(8~16文字)"
                                type="password"
                            />
                        </div>
                    <div class="c-password-modal__element">
                        <TextInput
                            v-model:value="passwordData.password"
                            errorKey="password"
                            type="password"
                            placeHolder="パスワード(8~16文字)"
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

.c-password-modal {
    &__container {
        position: fixed;
        // position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 2;
    }
    &__bg {
        background:#8080804d;
        position: absolute;
        height: 100%;
        width: 100%;
    }
    &__form {
        width: 100%;
        box-sizing: border-box;
        margin-top: 100px;
        margin-left: auto;
        margin-right: auto;
        padding: 20px 40px;
        box-sizing: border-box;
        background-color: #fff;
        box-shadow: 0 0 8px #ccc;
        border-radius: 1px;
        @include pc() {
            width: 450px;
        }
    }
    &__title {
        margin-bottom: 10px;
        padding: 10px;
        color: #494949;
        border-left: solid 5px #ffec5c;
    }

    &__element {
        margin-bottom: 15px;
    }
    &__btn-box {
        display: flex;
        padding: 0px;
        justify-content: center;
    }
}
</style>
