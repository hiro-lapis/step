<script setup lang="ts"></script>

<template>
    <div class="c-input-loading">
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
        <div class="c-input-loading__element">
            <div class="c-input-loading__element__item"></div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.c-input-loading {
    position: relative;
    transform: translate(-50%, -50%);
}
.c-input-loading .c-input-loading__element {
    position: absolute;
    top: calc(50% - 10px / 2);
    left: calc(50% - 10px / 2);
    transform-origin: -150% 50%;
}
.c-input-loading .c-input-loading__element .c-input-loading__element__item {
    width: 7px; // アイコン粒のサイズ
    height: 7px;
    animation: loading10 2s infinite cubic-bezier(0.16, 0.69, 0.85, 0.54);
    animation-delay: inherit;
    border-radius: 50%;
    // background: #bbb;
    background: #ab47bc;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 1) {
    transform: translateX(200%) rotate(-90deg);
    animation-delay: -1.5s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 2) {
    transform: translateX(200%) rotate(-45deg);
    animation-delay: -1.375s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 3) {
    transform: translateX(200%);
    animation-delay: -1.25s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 4) {
    transform: translateX(200%) rotate(45deg);
    animation-delay: -1.125s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 5) {
    transform: translateX(200%) rotate(90deg);
    animation-delay: -1s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 6) {
    transform: translateX(200%) rotate(135deg);
    animation-delay: -0.875s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 7) {
    transform: translateX(200%) rotate(180deg);
    animation-delay: -0.75s;
}
.c-input-loading .c-input-loading__element:nth-of-type(0n + 8) {
    transform: translateX(200%) rotate(225deg);
    animation-delay: -0.625s;
}
@keyframes loading10 {
    0%,
    100% {
        transform: scale(0);
    }
    50% {
        transform: scale(1);
    }
}
</style>
