<script setup lang="ts">
// props
defineProps({
    label : { required: true, type: String, },
    side : { required: true, type: String, validator: (v: string) => ['left', 'right'].includes(v), default: 'right' },
    value: { required: false, type:String, default: '', }, // 親と連携するvalue
    className: { required: false, type: String, default: '', },
})
// data
// computed
// methods
</script>
<template>
    <label class="c-single-checkbox">
        <input class="c-single-checkbox__input" type="checkbox">
        <span class="c-single-checkbox__cover"></span>
        <span class="c-single-checkbox__label-text">{{ label }}</span>
    </label>
</template>
<style scoped lang="scss">
$box-size: 20px;
.c-single-checkbox {
    &__input {
        margin: 0;
        width: 0;
        opacity: 0;
    }
    &__cover { // チェックボックスの見た目
        transition: all .3s;
        position: relative;
        top: 0;
        left: 0;
        display: inline-block;
        width: $box-size;
        height: $box-size;
        background: #F5F5F5;
        box-shadow: 0 1px 4px rgba(0,0,0, .4) inset;
    }
    &__label-text {
        margin-left: 5px;
        display: inline-block;
        vertical-align: text-top;
        font-size: 10px;
        font-weight: bold;
    }
    &:hover > .c-single-checkbox__cover {
        background: #ccc;
    }
    &:checked + .c-single-checkbox__cover { // チェック時の背景色
        background: #1da1f2;
    }
}

.c-single-checkbox__input:focus + .c-single-checkbox__cover{ // フォーカス時の背景
    background: #ccc;
}
.c-single-checkbox__input:checked + .c-single-checkbox__cover {  // チェック時の背景
    background: #1da1f2;
}
.c-single-checkbox__input:checked + .c-single-checkbox__cover::before { // チェック左側
    content: "";
    display: block;
    position: absolute;
    top: 35%;
    left: 50%;
    width: 35%;
    height: 2px;
    transform: translate(-5px, 6px) rotateZ(-135deg);
    transform-origin: 1px 1px;
    background: #fff;
}
.c-single-checkbox__input:checked + .c-single-checkbox__cover::after {// チェック右側
    content: "";
    display: block;
    position: absolute;
    top: 36%;
    left: 50%;
    width: 70%;
    height: 2px;
    transform: translate(-5px, 6px) rotateZ(-45deg);
    transform-origin: 1px 1px;
    background: #fff;
}
</style>
