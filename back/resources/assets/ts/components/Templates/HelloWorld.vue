<script setup lang="ts">
import { useUserStore } from '../../store/globalStore'

console.log('type script and pinia is now!')
// store
const store = useUserStore()
console.log(store)
// props
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <h1>Hello World !! and Type Script!!!</h1>
    <div>
        <router-link to="/good-morning">GoodMorning</router-link>
    </div>
    <div>
        <router-link to="/login">Login</router-link>
    </div>
</template>
