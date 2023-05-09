import { inject, ref } from 'vue'
import { defineStore } from 'pinia'

export const useTransparentOverlayStore = defineStore('transparentOverlay', () => {
    // data store.$reset()で初期化
    const active = ref(false)
    const isActivated = () => active.value
    // methods
    const toggle = () => active.value = active.value ? false : true
    const activate = () => active.value = true
    const deactivate = () => active.value = false
    return {
        active,
        isActivated,
        activate,
        toggle,
        deactivate
    }
})
