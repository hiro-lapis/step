<script setup lang="ts">
import { computed, inject } from 'vue'
import { useTransparentOverlayStore } from '../../store/components/TransparentOverlay'
const active = inject<boolean>('activeEditMenu', false)!
const overlay = useTransparentOverlayStore()
// computed
const transparentOverlay = computed(() =>  overlay.active)
</script>

<template>
	<transition>
		<div
			@click.stop.prevent="overlay.deactivate"
			v-if="transparentOverlay"
			class="c-edit-tool-tip-overlay"
		>
		</div>
	</transition>
</template>

<style lang="scss" scoped>
</style>
