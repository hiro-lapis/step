<script setup lang="ts"></script>

<template>
    <transition name="slide-fade-down">
        <slot></slot>
    </transition>
</template>

<style lang="scss" scoped>
.slide-fade-down-enter-active {
  transition: all 1s ease-in-out;
}
.slide-fade-down-leave-active {
  transition: all .3s ease;
}
.slide-fade-down-enter, .slide-fade-down-leave-to {
  transform: translateY(-100px);
  opacity: 0;
}
</style>
