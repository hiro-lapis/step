<script setup lang="ts">
// props
defineProps({
    message: { required: true, type: String, }, // クラス名(default: 横幅いっぱい表示のクラス)
})

</script>

<template>
    <transition>
        <span class="c-error-message">{{ message }}</span>
    </transition>
</template>

<style lang="scss" scoped>
// エラーメッセージエフェクト
.v-enter,
.v-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}
.v-enter-to,
.v-leave {
    opacity: 1;
    transform: translateY(100%);
}
.v-enter-active {
    transition: all 200ms;
}
.v-leave-active {
    transition: all 200ms;
}
</style>
