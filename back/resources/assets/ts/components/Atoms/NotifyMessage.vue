<script setup lang="ts">
import { computed, onMounted, onActivated } from 'vue'
import SlideDown from './Transition/SlideDown.vue'
import { useMessageInfoStore } from '../../store/globalStore'

const store = useMessageInfoStore()
// computed
const isError = computed(() => store.messageInfo.isError)
const showFlag = computed(() => store.messageInfo.message !== '')
const message = computed(() => store.messageInfo.message)

// メッセージ表示中にブラウザリロードした時にリセットされないのを防止
onMounted(() => store.setMessage(''))
onActivated(() => setTimeout(() => store.setMessage(''), 5000))
</script>

<template>
    <slide-down>
        <template v-if="showFlag">
            <!--  メッセージ内の改行も反映するためHTMLとして出力 -->
            <div
                class="c-slide-down-message__container"
                :class="{ 'c-slide-down-message__container--error': isError }"
                v-html="message"
            >
            </div>
        </template>
    </slide-down>
</template>
