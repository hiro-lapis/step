<script setup lang="ts">
import { inject, onMounted } from 'vue'
import LoadingIcon from '../Atoms/LoadingIcon.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { guestPageName } from '../../routes/routes'

// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>("$repositories")!
console.log(userStore)
// props
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <h1>Hello World !! and Type Script!!!</h1>
            <div>
                <router-link to="/good-morning">GoodMorning</router-link>
            </div>
            <div>
                <router-link to="/login">Login</router-link>
            </div>
        </template>
    </BaseView>
</template>
