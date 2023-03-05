import { computed } from 'vue'
import { defineStore } from 'pinia'
import { useLocalStorage } from '@vueuse/core'
import { User } from '../types/User'


export const useUserStore = defineStore('user', () => {
    const user = useLocalStorage<User>('user', { id: -1, name: 'hoge', image_url: '', email: 'hoge@gmail.com', profile: ''})
    let login = useLocalStorage('login', false)
    const setUser = (newUser: User) => user.value = newUser
    const isLogin = computed(() => login.value)
    const initUser = () => user.value = { id: -1, name: '', image_url: '', email: '', profile: ''}
    const setLogin = (status: boolean) => login.value = status
    return {
        user,
        login,
        isLogin,
        setUser,
        initUser,
        setLogin,
    }
})
