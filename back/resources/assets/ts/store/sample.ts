import { defineStore } from 'pinia'

// You can name the return value of `defineStore()` anything you want,
// but it's best to use the name of the store and surround it with `use`
// and `Store` (e.g. `useUserStore`, `useCartStore`, `useProductStore`)
// the first argument is a unique id of the store across your application
// storeを書くときは、useXXXXStore という名前で書く
// defineStore の第一引数は、store上でIDとして使われるため、ユニークな命名にする
export const useAlertsStore = defineStore('alerts', {
  // other options...
})

// Setup Stores

// Option API ライクな書き方とSetup関数の書き方の2通りがある
// ref() => stateのプロパティになる
// computed() => getters
// function() => getters
import { computed, ref } from 'vue'

export const useCounterStore = defineStore('counter', () => {
    const count = ref(0)
    const name = ref('Eduardo')
    const doubleCount = computed(() => count.value * 2)
    function increment() {
      count.value++
    }

    return { count, name, doubleCount, increment }
  })

// Using Store
// vueサイドで使う場合、importする

// <script setup>
// import { useCounterStore } from '@/stores/counter'

// // access the `store` variable anywhere in the component ✨
// const store = useCounterStore()
// </script>
