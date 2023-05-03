<script setup lang="ts">
import { ref } from 'vue'

// props
defineProps({
    openTxt: { required: false, type: String, default: 'open me' },
})
// data
const dialog = ref<HTMLDialogElement | null>(null)
const open = ref(false)
// methods
const openDialog = () => open.value = !open.value
const closeDialog = () => {
  open.value = false
  dialog.value!.close()
}
const closeOnOutside = (e) => {
const dialogDimensions = dialog.value?.getBoundingClientRect()!
  if (
    e.clientX < dialogDimensions.left ||
    e.clientX > dialogDimensions.right ||
    e.clientY < dialogDimensions.top ||
    e.clientY > dialogDimensions.bottom
  ) {
    open.value = false
  }
}
</script>

<template>
    <button @click="openDialog">{{ openTxt }}</button>
    <div @click="closeDialog" class="overlay">
        <dialog @click.stopPropagation="closeOnOutside" ref="dialog" :open="open">
            <span>You can see me</span>
            <button @click="closeDialog">close me</button>
        </dialog>
    </div>
</template>

<style lang="scss" scoped>
dialog {
  z-index: 10;
  margin-top: 10px;
  background-color: #f6f5f4;
  border: none;
  border-radius: 1rem;
}

dialog::backdrop { // dialogの背景
  background-color: hsl(250, 100%, 50%, 0.25);
}

</style>
