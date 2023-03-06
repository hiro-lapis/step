<script setup lang="ts">
import { useRequestStore } from '../../store/globalStore'
import { computed } from 'vue'

const requestStore = useRequestStore()
const isLoading = computed(() => requestStore.isLoading)
</script>

<template>
	<transition>
		<div v-if="isLoading" class="c-back-ground-cover">
			<div class="c-icon--loading">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</transition>
</template>

<style lang="scss" scoped>
// アニメーション スタイル開始
.v-enter, .v-leave-to {
	opacity: 0;
}
.v-enter-to, .v-leave {
	opacity: 1;
}
.v-enter-active {
	transition: opacity 400ms;
}
.v-leave-active {
	transition: opacity 600ms;
}
// アニメーション スタイル終了

.c-back-ground-cover {
	// ローディングアイコンの配置
	display: flex;
    align-items: center;
	justify-content: center;
	// カバーで全体を固定で覆う
	position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: #ffffff4d;
    z-index: 3;
}

.c-icon--loading {
	position: absolute;
	top: 50%;
	left: 50%;
	width: 40px;
	height: 40px;
	transform: translate(-50%,-50%);
	& span {
		display: block;
		position: absolute;
		width: 50%;
		height: 50%;
		border-radius: 50%;
	}
	& span:nth-child(1) {
		top: 0;
		left: 0;
		animation: loading07_mv1 3s cubic-bezier(.11,.75,.48,.94) infinite;
		background-color: rgba(76,207,224,0.75);
	}
	& span:nth-child(2) {
		top: 0;
		right: 0;
		animation: loading07_mv2 3s cubic-bezier(.11,.75,.48,.94) infinite;
		background-color: rgba(174,213,129,0.75);
	}
	& span:nth-child(3) {
		bottom: 0;
		left: 0;
		animation: loading07_mv3 3s cubic-bezier(.11,.75,.48,.94) infinite;
		background-color: rgba(255,213,79,0.75);
	}
	& span:nth-child(4) {
		bottom: 0;
		right: 0;
		animation: loading07_mv4 3s cubic-bezier(.11,.75,.48,.94) infinite;
		background-color: rgba(240,98,146,0.75);
	}
}
@keyframes loading07_mv1 {
	0% {
		z-index: 10;
		transform: translate(0);
	}
	25% {
		transform: translate(100%, 0);
	}
	50% {
		transform: translate(100%, 100%);
	}
	75% {
		z-index: 0;
		transform: translate(0, 100%);
	}
	100% {
		transform: translate(0);
	}
}
@keyframes loading07_mv2 {
	0% {
		transform: translate(0);
	}
	25% {
		transform: translate(0, 100%);
	}
	50% {
		z-index: 0;
		transform: translate(-100%, 100%);
	}
	75% {
		z-index: 10;
		transform: translate(-100%, 0);
	}
	100% {
		transform: translate(0);
	}
}
@keyframes loading07_mv3 {
	0% {
		transform: translate(0);
	}
	25% {
		z-index: 10;
		transform: translate(0, -100%);
	}
	50% {
		transform: translate(100%, -100%);
	}
	75% {
		transform: translate(100%, 0);
	}
	100% {
		z-index: 0;
		transform: translate(0);
	}
}
@keyframes loading07_mv4 {
	0% {
		transform: translate(0);
	}
	25% {
		z-index: 0;
		transform: translate(-100%, 0);
	}
	50% {
		z-index: 10;
		transform: translate(-100%, -100%);
	}
	75% {
		transform: translate(0, -100%);
	}
	100% {
		transform: translate(0);
	}
}
</style>
