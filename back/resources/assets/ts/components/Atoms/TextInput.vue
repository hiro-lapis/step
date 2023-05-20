
<script setup lang="ts">
import { computed, ref } from 'vue'
import ErrorMessage from './ErrorMessage.vue'
import RequirementText from './RequirementText.vue'

// props
const props = defineProps({
    className: { required: false, type: String, default: 'c-input--large', }, // クラス名(default: 横幅いっぱい表示のクラス)
    errorKey: { required: false, type: String, default: '' }, // TODO: 削除
    errorMessage: { required: false, type: String, default: '' }, // エラーメッセージ
    formId: { required: false, type: String, default: '', }, // ラベルとinputを紐付けるid
    inline: { required: false, type: Boolean, default: false, }, // エラーメッセージ不要な時にインライン化
    disableShowPassword: { required: false, type: Boolean, default: false, }, // PW表示アイコン非表示
    label: { required: false, type:String, default: '', }, // 入力ラベル
    placeHolder: { required: false, type: String, default: '' }, // プレースホルダー
    required: { required: false, type: Boolean, default: false, }, // 入力必須マーク
    optional: { required: false, type: Boolean, default: false, }, // 入力任意マーク
    rules: { required: false, type: Array<Function>, default: []}, // バリデーションルール関数
    type: { required: false, type: String, default: 'text' }, // inputのtype属性
    value: { required: false, type:String, default: '', }, // 編集画面などの初期値
})

// emits
interface Emits {
    (e: 'update:value', value: string): void
    (e: 'keyupEnter'): void
    (e: 'keyDown:shiftEnter'): void
}
const emit = defineEmits<Emits>()
// data
// const errorMessage = ref('')
const showPassowrd = ref(false)
// computed
const existsError = computed(() => props.errorMessage !== '')
const isTypePassowrd = computed(() => props.type === 'password')
const passwordIcon = computed(() => showPassowrd.value ? 'fa-eye-slash' : 'fa-eye')
const inputType = computed(() => {
    if (!isTypePassowrd.value) return props.type
    // パスワード(設定値)かつパスワード表示モードかどうかでinput typeを変更
    return showPassowrd.value && !props.disableShowPassword ? 'text' : 'password'
})

// methods
const input = (event: Event) => {
    const val = (event.target as HTMLInputElement).value
    emit('update:value', val)
}
const hidePassword = () => {
    showPassowrd.value = false
}
// enter押下後にイベント発行 処理実行するかどうかは親側の実装に任せる
const handleEnter = () => emit('keyupEnter')
const emitKeyPressShiftEnter = () => {
    emit('keyDown:shiftEnter')
}

// 親から呼び出せるよう公開
defineExpose({
    hidePassword,
})
</script>

<template>
    <div class="c-input__container">
        <div class="c-input__label">
            <!-- エラーメッセージのキー設定時のみ表示 -->
            <template v-if="label">
                <label :for="formId" class="c-label">
                    {{ label }}
                    <slot name="asideLabel"></slot>
                    <template v-if="required">
                        <RequirementText />
                    </template>
                    <template v-if="optional">
                        <RequirementText :isRequired="false" />
                    </template>
                </label>
            </template>
        </div>
        <div class="c-input__body">
            <input
                :value="value"
                :type="inputType"
                @input="$event => input($event)"
                @keyup.enter="handleEnter()"
                @keydown.shift.enter="emitKeyPressShiftEnter"
                class="c-input"
                :id="formId"
                :class="[{'c-message--no-error': inline}, className ]"
                :placeholder="placeHolder"
            />
            <!-- パスワード表示ボタン -->
            <template v-if="isTypePassowrd">
                <span class="c-input__password-icon">
                    <i
                        @click="showPassowrd = !showPassowrd"
                        class="c-icon--eye fa"
                        :class="passwordIcon"></i>
                </span>
            </template>
        </div>
        <!-- エラーメッセージのキー設定時のみ表示 -->
        <template v-if="existsError">
            <ErrorMessage :message="errorMessage" />
        </template>
    </div>
</template>
