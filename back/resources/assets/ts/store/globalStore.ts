import { defineStore } from 'pinia'
import { useLocalStorage } from '@vueuse/core'
import { User } from '../types/User'


export const useUserStore = defineStore('user', () => {
    const user = useLocalStorage<User>('user', { id: -1, name: 'hoge', image_url: '', email: 'hoge@gmail.com', profile: ''})
    const setUser = (newUser: User) => user.value = newUser
    return {
        user,
        setUser
    }
})
