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
    <div class="c-search-box">
      <input
        type="text"
        @input="$event => input($event)"
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
