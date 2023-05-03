<script setup lang="ts">
import { computed, PropType, ref } from 'vue'
import { Menu } from '../../types/components/Menu'
// props
const props = defineProps({
    menus: { required: false, type: Array as PropType<Menu[]>, default: () => [] },
})
// data
const active = ref(false)
// computed
const bottomVal = computed(() => {
    return (props.menus!.length * -50).toString() + 'px'
})
</script>

<template>
    <div class="c-edit-tool-tip">
        <i
            @click="active = !active"
            class="c-icon--elipsis fa fa-ellipsis-h"
            :class="{ activated: active }"
        ></i>
        <div class="c-edit-tool-tip__menus" :style="{ bottom: bottomVal }">
            <template v-for="menu in menus">
                <router-link :to="menu.to" v-if="menu.to">
                    <p class="c-edit-tool-tip__txt u-margin-b-1p">
                        <i v-if="menu.icon" :class="menu.icon"></i>
                        <span>{{ menu.name }}</span>
                    </p>
                </router-link>
            </template>
            <!-- 削除ボタンなどページ遷移なしのUI挿入slot -->
            <slot name="bottom"></slot>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.c-edit-tool-tip {
    display: inline-block;
    position: relative;
    &__menus {
        display: flex;
        justify-content: center;
        flex-direction: column; // メニューを縦に
        align-items: center;
        visibility: hidden;
        opacity: 0;
        position: absolute;
        // bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        padding: .5em 1em;
        border-radius: 3px;
        background-color: #ebebeb;
        color: #404040;
        font-size: .7em;
        white-space: nowrap;
        transition: opacity .3s;
    }
    &__txt {
        font-size: 12px;
        &:hover {
            text-decoration: underline;
        }
    }
}

// .c-edit-tool-tip__menus {
    // display: flex;
    // justify-content: center;
    // flex-direction: column; // メニューを縦に
    // align-items: center;
    // visibility: hidden;
    // opacity: 0;
    // position: absolute;
    // // bottom: -30px;
    // left: 50%;
    // transform: translateX(-50%);
    // padding: .5em 1em;
    // border-radius: 3px;
    // background-color: #ebebeb;
    // color: #404040;
    // font-size: .7em;
    // white-space: nowrap;
    // transition: opacity .3s;
// }

.c-edit-tool-tip > div::before {
    position: absolute;
    top: -6px;
    width: 9px;
    height: 6px;
    background-color: inherit;
    clip-path: polygon(50% 0, 0 100%, 100% 100%);
    content: '';
}

.activated + div {
    visibility: visible;
    opacity: 1;
}
</style>
