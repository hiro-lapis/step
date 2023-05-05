
<script setup lang="ts">
import { computed, ref } from 'vue'
import ErrorMessage from './ErrorMessage.vue'
import RequiredBadge from './RequiredBadge.vue'


// props
const props = defineProps({
    className: { required: false, type: String, default: 'c-input--large', }, // クラス名(default: 横幅いっぱい表示のクラス)
    errorKey: { required: false, type: String, default: '' },
    formId: { required: false, type: String, default: '', }, // ラベルとinputを紐付けるid
    inline: { required: false, type: Boolean, default: false, }, // エラーメッセージ不要な時にインライン化
    label: { required: false, type:String, default: '', }, // 入力ラベル
    placeHolder: { required: false, type: String, default: '' }, // プレースホルダー
    required: { required: false, type: Boolean, default: false, }, // 入力必須マーク
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

// computed
const existsError = computed(() => errorMessage.value !== '')

// data
const errorMessage = ref('')

// methods
const input = (event: Event) => {
    const val = (event.target as HTMLInputElement).value
    // 受け取ったルールでバリデーション
    props.rules.forEach(fn => {
        const result = fn(val)
        errorMessage.value = typeof result === 'string' ? result : ''
    })
    emit('update:value', val)
}
// enter押下後にイベント発行 処理実行するかどうかは親側の実装に任せる
const handleEnter = () => emit('keyupEnter')
const emitKeyPressShiftEnter = () => {
    emit('keyDown:shiftEnter')
}
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
                        <RequiredBadge />
                    </template>
                </label>
            </template>
            <!-- エラーメッセージのキー設定時のみ表示 -->
            <template v-if="existsError">
                <ErrorMessage :message="errorMessage" />
            </template>
        </div>
        <input
            :value="value"
            :type="type"
            @input="$event => input($event)"
            @keyup.enter="handleEnter()"
            @keydown.shift.enter="emitKeyPressShiftEnter"
            class="c-input"
            :id="formId"
            :class="[{noErrorMessage: inline}, className ]"
            :placeholder="placeHolder"
        />
    </div>
</template>
