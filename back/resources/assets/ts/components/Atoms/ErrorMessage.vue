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
}
.v-enter-to,
.v-leave {
    opacity: 1;
}
.v-enter-active {
    transition: opacity 100ms;
}
.v-leave-active {
    transition: opacity 100ms;
}

// エラーメッセージがない時はラベルとインプットのスペースをなくす
.noErrorMessage {
    position: relative;
    top: 0;
}
</style>
