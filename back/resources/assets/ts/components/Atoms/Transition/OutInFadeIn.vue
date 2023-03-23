<script setup lang="ts">
</script>

<template>
    <transition mode="out-in">
        <slot></slot>
    </transition>
</template>


<style scoped>
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
</style>
