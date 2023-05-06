import { computed, reactive, ref } from 'vue'
import { defineStore } from 'pinia'
import { useLocalStorage } from '@vueuse/core'
import { User } from '../types/User'

export const useUserStore = defineStore('user', () => {
    // data
    const user = useLocalStorage<User>('user',
        { id: -1, name: '', image_url: '', email: 'hoge@gmail.com', profile: '' }
    )
    const challengingStepIds = useLocalStorage<number[]>('challengingStepIds', [])
    const remainCount = useLocalStorage<number>('remainCount', 0)

    const login = useLocalStorage('login', false)
    // computed
    const isLogin = computed(() => login.value)
    const existsRemainCount = computed(() => remainCount.value > 0)
    const skipApiConfirm = computed(() => user.value.skip_api_confirm)
    // methods
    const initUser = () => user.value = { id: -1, name: '', image_url: '', email: '', profile: ''}
    const setUser = (newUser: User) => user.value = newUser
    const setRemainCount = (newRemainCount: number) => remainCount.value = newRemainCount
    const initRemainCount = () => remainCount.value = 0
    const setChallengingStepId = (newChallengingStepId: number) => {
        if (challengingStepIds.value.includes(newChallengingStepId)) {
            return
        }
        challengingStepIds.value.push(newChallengingStepId)
    }
    // 初期化した上で、新しいstepIdを追加
    const setChallengingStepIds = (newChallengingStepIds: number[]) => {
        challengingStepIds.value = []
        newChallengingStepIds.forEach((stepId) => {
            if (challengingStepIds.value.includes(stepId)) {
                return // 次のループへ
            }
            challengingStepIds.value.push(stepId)
        })
    }
    const popChallengingStepId = (stepId: number) => {
        const index = challengingStepIds.value.indexOf(stepId)
        if (index !== -1) {
            challengingStepIds.value.splice(index, 1)
        }
    }
    const initChallengingStepIds = () => challengingStepIds.value = []
    const isInChallenge = (stepId: number) => challengingStepIds.value.includes(stepId)
    const setLogin = (status: boolean) => login.value = status
    return {
        user,
        login,
        isLogin,
        challengingStepIds,
        existsRemainCount,
        skipApiConfirm,
        remainCount,
        setRemainCount,
        initRemainCount,
        setLogin,
        setUser,
        initUser,
        setChallengingStepId,
        setChallengingStepIds,
        popChallengingStepId,
        initChallengingStepIds,
        isInChallenge,
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
        // 10s後にリセット
        setTimeout(() => messageInfo.message = '', 10000)
    }
    const setErrorMessage = (message: string) => {
        messageInfo.message = message
        messageInfo.isError = true
        // 10s後にリセット
        setTimeout(() => messageInfo.message = '', 10000)
    }
    return {
        messageInfo,
        hasMessage,
        setMessage,
        setErrorMessage,
    }
})

export const useConfirmDialogStore = defineStore('dialog', () => {
    // data
    const dialog = ref(false)
    // computed
    const open = computed(() => dialog.value)
    // method
    const setDialog = (state: boolean) => dialog.value = state
    return {
        dialog,
        open,
        setDialog,
    }

})
