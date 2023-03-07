import { computed, reactive, ref } from 'vue'
import { defineStore } from 'pinia'
import { reactify, useLocalStorage } from '@vueuse/core'
import { User } from '../types/User'

export const useUserStore = defineStore('user', () => {
    // data
    const user = useLocalStorage<User>('user', { id: -1, name: 'hoge', image_url: '', email: 'hoge@gmail.com', profile: ''})
    const login = useLocalStorage('login', false)
    // computed
    const isLogin = computed(() => login.value)
    // computed
    const initUser = () => user.value = { id: -1, name: '', image_url: '', email: '', profile: ''}
    const setUser = (newUser: User) => user.value = newUser
    const setLogin = (status: boolean) => login.value = status
    return {
        user,
        login,
        isLogin,
        setLogin,
        setUser,
        initUser,
    }
})

export const useRequestStore = defineStore('request', () => {
    // data
    const loading = ref(false)
    // computed
    const isLoading = computed(() => loading.value)
    // method
    const setLoading = (state: boolean) => loading.value = state
    return {
        loading,
        isLoading,
        setLoading,
    }
})

type messageInfo = {
    message: string,
    isError: boolean,
}

export const useMessageInfoStore = defineStore('messageInfo', () => {
    // data
    const messageInfo = reactive<messageInfo>({ message: '', isError: false})
    // computed
    const hasMessage = () => messageInfo.message !== ''
    // computed
    const setMessage = (message: string) => {
        messageInfo.message = message
        messageInfo.isError = false
        // 5s後にリセット
        setTimeout(() => messageInfo.message = '', 5000)
    }
    const setErrorMessage = (message: string) => {
        messageInfo.message = message
        messageInfo.isError = true
        // 5s後にリセット
        setTimeout(() => messageInfo.message = '', 5000)
    }
    return {
        messageInfo,
        hasMessage,
        setMessage,
        setErrorMessage,
    }
})
