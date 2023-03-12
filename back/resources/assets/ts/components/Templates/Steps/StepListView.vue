<script setup lang="ts">
import { inject, onMounted, ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import { Repositories } from '../../../apis/repositoryFactory'

// utilities
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>("$repositories")!
const router = useRouter()
const route = useRoute()

console.log('type script and pinia is now!')
// store
// props
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <h1>This is step list</h1>
    <h1>Hello World !! and Type Script!!!</h1>
    <div>
        <router-link to="/good-morning">GoodMorning</router-link>
    </div>
    <div>
        <router-link to="/login">Login</router-link>
    </div>
</template>
