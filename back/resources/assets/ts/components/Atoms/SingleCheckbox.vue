<script setup lang="ts">
// props
defineProps({
    label : { required: true, type: String, },
    side : { required: true, type: String, validator: (v: string) => ['left', 'right'].includes(v), default: 'right' },
    value: { required: false, type: Boolean, default: false, }, // 親と連携するvalue
    className: { required: false, type: String, default: '', },
})

// emits
interface Emits {
    (e: 'update:value', checked: boolean): void;
}
const emit = defineEmits<Emits>()
// methods
const input = (event: Event) => {
    const val = (event.target as HTMLInputElement).checked
    emit('update:value', val)
}
</script>

<template>
    <label class="c-checkbox--single">
        <input
            class="c-checkbox--single__input"
            type="checkbox"
            :value="value"
            :checked="value"
            @change="input"
        >
        <span class="c-checkbox--single__cover"></span>
        <span class="c-checkbox--single__label-text">{{ label }}</span>
    </label>
</template>

<style scoped lang="scss">
</style>
