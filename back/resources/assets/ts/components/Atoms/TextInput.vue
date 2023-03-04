
<script setup lang="ts">
import { computed, ref } from 'vue'
import ErrorMessage from './ErrorMessage.vue'


// props
const props = defineProps({
    className: { required: false, type: Boolean, default: 'c-input--large', }, // クラス名(default: 横幅いっぱい表示のクラス)
    errorKey: { required: false, type: String, default: '' },
    formId: { required: false, type: String, default: '', }, // ラベルとinputを紐付けるid
    inline: { required: false, type: Boolean, default: '', }, // エラーメッセージ不要な時にインライン化
    label: { required: false, type:String, default: '', }, // 入力ラベル
    placeHalder: { required: false, type: String, default: '' }, // プレースホルダー
    required: { required: false, type: Boolean, default: '', }, // 入力必須マーク
    rules: { required: false, type: Array<Function>, default: () => null}, // バリデーションルール関数
    type: { required: false, type: String, default: 'text' }, // inputのtype属性
    value: { required: false, type:String, default: '', }, // 編集画面などの初期値
})

// emits
interface Emits {
    (e: 'update:value', value: string): void;
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
    emit('update:value', (event.target as HTMLInputElement).value)
}

</script>


<template>
    <div class="c-input__container">
        <div class="c-input__label">
            <!-- エラーメッセージのキー設定時のみ表示 -->
            <template v-if="label">
                <label :for="formId" class="c-label">
                    {{ label }}
                    <template v-if="required">
                        <required-badge-component />
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
            class="c-input"
            :id="formId"
            :class="[{noErrorMessage: inline}, className ]"
            :placeholder="placeHalder"
        />
    </div>
</template>
