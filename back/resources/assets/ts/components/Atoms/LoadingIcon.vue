<script setup lang="ts">
import { useRequestStore } from '../../store/globalStore'
import { computed } from 'vue'

const requestStore = useRequestStore()
const isLoading = computed(() => requestStore.isLoading)
</script>

<template>
	<transition>
		<div v-if="isLoading" class="c-icon--loading__bg-layer">
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
</style>
