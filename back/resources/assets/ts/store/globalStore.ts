import { computed, reactive, ref } from 'vue'
import { defineStore } from 'pinia'
import { reactify, useLocalStorage } from '@vueuse/core'
import { User } from '../types/User'

export const useUserStore = defineStore('user', () => {
    // data
    const user = useLocalStorage<User>('user', { id: -1, name: 'hoge', image_url: '', email: 'hoge@gmail.com', profile: ''})
    const login = useLocalStorage('login', false)
    // const notifyMessage = ref('')
    // computed
    const isLogin = computed(() => login.value)
    // const hasNotifyMessage = () => notifyMessage.value !== ''
    // computed
    const initUser = () => user.value = { id: -1, name: '', image_url: '', email: '', profile: ''}
    const setUser = (newUser: User) => user.value = newUser
    const setLogin = (status: boolean) => login.value = status
    // const setNotifyMessage = (message: string) => notifyMessage.value = message
    return {
        user,
        login,
        // notifyMessage,
        isLogin,
        // hasNotifyMessage,
        setLogin,
        setUser,
        // setNotifyMessage,
        initUser,
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
    const setMessageInfo = (data: messageInfo) => {
        messageInfo.message = data.message
        messageInfo.isError = data.isError
    }
    return {
        messageInfo,
        hasMessage,
        setMessageInfo,
    }
})
