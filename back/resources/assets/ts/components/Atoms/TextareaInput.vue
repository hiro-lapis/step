<script setup lang="ts">
import { computed, ref } from 'vue'
import RequiredBadge from './RequiredBadge.vue'
import ErrorMessage from './ErrorMessage.vue'

// props
const props = defineProps({
    className: { required: false, type: String, default: 'c-textarea', },
    formId: { required: false, type: String, default: '', },
    label: { required: true, type: String, },
    errorMessage: { required: true, type: String, },
    placeHolder: { required: false, type: String, default: '', },
    required: { required: false, type: Boolean, default: false },
    counter: { required: false, type: Boolean, default: false },
    max: { required: false, type: Number, default: null },
    height: { required: false, type: String, default: '100' }, // textareaの高さ(px)
    rules: { required: false, type: Array<Function>, default: []}, // バリデーションルール関数
    value: { required: true, type: String, default: '' },
})
interface Emit {
    (e: 'update:value', value: string)
    (e: 'keyDown:shiftEnter')

}
const emit = defineEmits<Emit>()

// data
// computed
const count = computed(() => props.value?.length ?? 0 )
const existsError = computed(() => props.errorMessage !== '')
// methods
const input = (event: Event) => {
    const val = (event.target as HTMLInputElement).value?.trim() ?? ''
    emit('update:value', val)
}
const emitKeyPressShiftEnter = () => {
    emit('keyDown:shiftEnter')
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
                <slot name="labelAside"></slot>
                <template v-if="counter">
                    <span class="c-textare__counter">({{ count }} 文字)</span>
                </template>
            </label>
        </template>
        <!-- cols/rowsは指定せず、スタイリングで調整 -->
        <textarea
            :value="value"
            @keydown.shift.enter="emitKeyPressShiftEnter"
            @input="$event => input($event)"
            :id="formId"
            :class="className"
            :placeholder="placeHolder"
            :required="required"
            :style=" { height: height + 'px' }"
        />
        <template v-if="existsError">
            <ErrorMessage :message="errorMessage" />
        </template>
    </div>
</template>

<style lang="scss" scoped>
</style>
