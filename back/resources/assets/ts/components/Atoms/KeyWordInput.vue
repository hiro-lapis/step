<script setup lang="ts">
const props = defineProps({
    placeHolder: { type: String, default: '',  },
    value: { required: false, type:String, default: '', }, // 編集画面などの初期値
})

// emits
interface Emits {
    (e: 'update:value', value: string): void
    (e: 'clickIcon', value: string): void
    (e: 'keyupEnter', value: string): void
}
const emit = defineEmits<Emits>()

const input = (event: Event) => {
  const val = (event.target as HTMLInputElement).value
    emit('update:value', val)
}
const handleClick = () => {
    emit('clickIcon', props.value)
}
const handleEnter = () => emit('keyupEnter', props.value)
</script>

<template>
    <!-- <div class="c-search-box"> -->
    <div class="c-input--keyword__container">
      <input
        type="text"
        name="key-word"
        @input="$event => input($event)"
        :placeholder="placeHolder"
        @keyup.enter="handleEnter"
        class="c-input--keyword"
      />
      <div class="c-icon--search" @click="handleClick">
        <i class="fas fa-search"></i>
      </div>
    </div>
  </template>

  <style scoped lang="scss">
  </style>
