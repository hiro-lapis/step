<script setup lang="ts">
// props
defineProps({
    title: { required: false, type: String, default: ''},
    closeBtnText: { required: false, type: String, default: '閉じる' },
})
// emits
interface Emits {
    (e: 'close'): void
}
const emit = defineEmits<Emits>()
// computed
// methods
const handleClose = () => emit('close')

const stopClose = (event: Event) => {
    event.stopPropagation()
}
</script>

<template>
    <Transition>
        <aside class="c-common-modal__container">
            <div @click="handleClose" class="c-common-modal__bg">
                <div @click="stopClose" class="c-common-modal__body">
                <template v-if="title">
                    <h3 class="c-common-modal__title">{{ title }}</h3>
                </template>
                    <slot name="content"></slot>
                    <div class="c-common-modal__btn-box">
                        <button @click="handleClose">
                            {{ closeBtnText }}
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </Transition>
</template>

<style lang="scss" scoped>
@import "../../../sass/foundation/_breakpoints.scss";

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
