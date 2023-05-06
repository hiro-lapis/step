<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
    placeHolder: { type: String, default: 'Search...',  },
    value: { required: false, type:String, default: '', }, // 編集画面などの初期値
})

// emits
interface Emits {
    (e: 'input', value: string): void
    (e: 'keyupEnter'): void
}
const emit = defineEmits<Emits>()
const searchValue = ref('');

const handleInput = (event) => {
    searchValue.value = event.target.value;
    emit('input', searchValue.value)
}
const handleClick = () => {
    emit('input', searchValue.value)
}
const handleEnter = () => emit('keyupEnter')
</script>

<template>
    <div class="c-search-box">
      <input
        type="text"
        v-model="searchValue"
        @input="handleInput"
        :placeholder="placeHolder"
        @keyup.enter="handleEnter"
        class="c-input--search"
      />
      <div class="c-search-icon" @click="handleClick">
        <i class="fas fa-search"></i>
      </div>
    </div>
  </template>

  <style scoped lang="scss">
  .c-search-box {
    position: relative;
    display: flex;
    align-items: center;
    margin: 10px;
    padding: 5px;
    border-radius: 5px;
  }

  .c-search-icon {
    position: absolute;
    top: 50%;
    right: 5px;
    color: #333;
    transform: translateY(-50%);
    height: 20px;
    display: flex;
    align-items: center;
    padding: 5px;
    cursor: pointer;
  }
  </style>
