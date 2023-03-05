<template>
    <span class="c-border-line c-border-line--large"></span>
</template>

<style lang="scss" scoped>
@import '../../../sass/foundation/breakpoints';
.c-border-line {
    content: "";
    height: 0px;
    border: solid 1px #ccc;
    display: inline-block;
    &--large {
        width: 100%;
    }
}
</style>
