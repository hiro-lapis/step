<script lang="ts" setup>
import { computed, inject, ref } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import { repositoryKey } from '../../types/common/Injection'

// utility
const $repositories = inject<Repositories>(repositoryKey)!
// props
const props = defineProps({
    previewUrl: { required: false, type: String, default: ''},
    previewMode: { required: false, type: Boolean, default: true},
})

interface Emits {
    (e: 'update:previewUrl', previewUrl: string): void
}
const emit = defineEmits<Emits>()

// data
const previewUrl = ref('')
// computed
const showPreview = computed(() => {
    return props.previewMode && previewUrl.value !== ''
})
// methods
const handleFileSelect = async (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return
    if (!validateFile(file)) {
        alert('ファイルの拡張子は.jpgまたは.pngで、サイズは10MB以下である必要があります。')
        return
    }
    try {
            const presignedResponse = await $repositories.file.getSignedUrl(file.name)
        try {
            await $repositories.file.upload(
                presignedResponse.data.presigned_url,
                file
            )
            const uploadUrl = presignedResponse.data.upload_path
            const res = await $repositories.file.download(uploadUrl)
            previewUrl.value = res.data.presigned_url
            emit('update:previewUrl', res.data.presigned_url)
        } catch (error) {
            console.error('アップロードエラー:', error)
            alert('ファイルのアップロードに失敗しました。')
        }
    } catch (error) {
        throw new Error('署名付きURLの取得に失敗しました。')
    }
}

const validateFile = (file: File): boolean => {
    const allowedExtensions = ['jpg', 'jpeg', 'png']
    const maxSize = 10 * 1024 * 1024 // 10MB
    const extension = file.name.split('.').pop()?.toLowerCase()!
    // 拡張子とサイズのバリデーション
    if (!allowedExtensions.includes(extension)) return false
    if (file.size > maxSize) return false
    return true
}
</script>

<template>
    <div>
      <input type="file" @change="handleFileSelect" accept="image/jpeg, image/png">
      <div v-if="showPreview">
        <img :src="previewUrl" alt="Uploaded Image">
      </div>
    </div>
</template>
