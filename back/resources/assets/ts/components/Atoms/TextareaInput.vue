<script setup lang="ts">
import { computed, ref } from 'vue'
import RequiredBadge from './RequiredBadge.vue'

// props
const props = defineProps({
    className: { required: false, type: String, default: 'c-textarea', },
    formId: { required: false, type: String, default: '', },
    label: { required: true, type: String, },
    placeHolder: { required: false, type: String, default: '', },
    required: { required: false, type: Boolean, default: false },
    counter: { required: false, type: Boolean, default: false },
    max: { required: false, type: Number, default: null },
    rules: { required: false, type: Array<Function>, default: []}, // バリデーションルール関数
    value: { required: true, type: String, },
})
interface Emit {
    (e: 'update:value', value: string)
}
const emit = defineEmits<Emit>()

// data
const errorMessage = ref('')
// computed
const existsError = computed(() => errorMessage.value !== '')
const count = computed(() => props.value.trim().length )
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
</script>

<template>
    <div class="c-textarea__container">
        <template v-if="label">
            <label class="c-textarea__label" :for="formId">
                {{ label }}
                <template v-if="required">
                    <RequiredBadge />
                </template>
                <template v-if="counter">
                    <span class="c-textare__counter">({{ count }} 文字)</span>
                </template>
            </label>
        </template>
        <!-- エラーメッセージのキー設定時のみ表示 -->
        <template v-if="existsError">
            <ErrorMessage :message="errorMessage" />
        </template>
        <!-- cols/rowsは指定せず、スタイリングで調整 -->
        <textarea
            :value="value"
            @input.trim="$event => input($event)"
            :id="formId"
            :class="className"
            :placeholder="placeHolder"
            :required="required"
        />
    </div>
</template>


<style lang="scss" scoped>

// scssに移すの禁止
// InputComponentでのみ付与したいスタイリングのため
.c-textarea {
    &__container {
        position: relative;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
    }
    &__counter {
        color: #999;
    }
    &__label {
        text-align: left;
        display: block;
        padding-left: 5px;
        margin-bottom: 5px;
    }
    &__loading-icon {
        position: absolute;
        top: 40%;
        left: 50%;
    }
}

// エラーメッセージがない時はラベルとインプットのスペースをなくす
.noErrorMessage {
    position: relative;
    top: 0;
}
</style>
