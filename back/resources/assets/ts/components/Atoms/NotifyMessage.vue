<script setup lang="ts">
import { computed, onMounted, onActivated } from 'vue'
import SlideDown from './Transition/SlideDown.vue'
import { useMessageInfoStore } from '../../store/globalStore'

const store = useMessageInfoStore()

const isError = computed(() => store.messageInfo.isError)
const showFlag = computed(() => store.messageInfo.message !== '')
const message = computed(() => store.messageInfo.message)


// メッセージ表示中にブラウザリロードした時にリセットされないのを防止
onMounted(() => store.setMessage(''))
onActivated(() => setTimeout(() => store.setMessage(''), 5000))
// onMounted(() => setTimeout(() => store.setMessage(''), 5000))
</script>

<template>
    <slide-down>
        <template v-if="showFlag">
            <div
                class="c-slide-down-message__container"
                :class="{ 'c-slide-down-message__container--error': isError }"
            >
                <!--  メッセージ内の改行も反映するためHTMLとして出力 -->
                <span class="c-slide-down-message" v-html="message"></span>
            </div>
        </template>
    </slide-down>
</template>

<style lang="scss" scoped>
@import '../../../sass/foundation/breakpoints';
.c-slide-down-message {
    white-space: pre;
    &__container {
        z-index: 5;
        position: fixed;
        top: 50px;
        width: 100%;
        padding: 10px 20px;
        background: #65c99b;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 1.5;
        text-align: center;
        white-space: pre;
        color: #fff;
        &--error {
        // variable.scss dizzy
        background-color: rgba(251, 100, 66, 1);
        }
    }
    // 	TODO: メッセージをいい感じにキラッとさせる。成功した時だけ
    // https://yuntu-tek.com/button-shine/
}
</style>
