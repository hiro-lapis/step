<script lang="ts" setup>
import { computed, inject, ref } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import { repositoryKey } from '../../types/common/Injection'
import RequirementText from './RequirementText.vue'
import { useMessageInfoStore } from '../../store/globalStore'

// utility
const $repositories = inject<Repositories>(repositoryKey)!
const messageStore = useMessageInfoStore()
// props
const props = defineProps({
    label: { required: false, type: String, default: ''},
    required: { required: false, type: Boolean, default: false},
    optional: { required: false, type: Boolean, default: false},
    previewUrl: { required: false, type: String, default: ''},
    previewMode: { required: false, type: Boolean, default: true},
    recommendSizeText: { required: false, type: String, default: ''},
})

interface Emits {
    (e: 'update:previewUrl', previewUrl: string): void
}
const emit = defineEmits<Emits>()

// data
const previewUrl = ref('')
const fileInputRef = ref<InstanceType<typeof HTMLInputElement>>()

// computed
const showPreview = computed(() => {
    return props.previewMode && previewUrl.value !== ''
})
// methods
const handleFileSelect = async (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return
    if (!validateFile(file)) {
        alert('ファイルの拡張子は.jpegまたは.pngで、サイズは10MB以下である必要があります')
        return
    }
    try {
        const presignedResponse = await $repositories.file.getSignedUrl(file.name)
        try {
            await $repositories.file.upload(presignedResponse.data.presigned_url, file)
            const uploadUrl = presignedResponse.data.upload_path
            const res = await $repositories.file.download(uploadUrl)
            previewUrl.value = res.data.presigned_url
            emit('update:previewUrl', res.data.presigned_url)
            messageStore.setMessage('画像のアップロードに成功しました')
            fileInputRef.value!.blur()

        } catch (error) {
            console.error('アップロードエラー:', error)
            alert('ファイルのアップロードに失敗しました')
        }
    } catch (error) {
        throw new Error('署名付きURLの取得に失敗しました')
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
const reset = () => {
    previewUrl.value = ''
    fileInputRef.value!.value = ''
    emit('update:previewUrl', '')
}
</script>

<template>
    <div class="c-input--presigned-upload__container">
        <div class="c-input--presigned-upload__label">
            <span class="c-label" for="image-upload">
                {{ label }}
                <template v-if="required">
                    <RequirementText />
                </template>
                <template v-else-if="optional">
                    <RequirementText  :isRequired="false" showOptional />
                </template>
                <template v-if="previewUrl">
                    <!-- reset button -->
                    <button type="button" class="c-button--reset" @click.stop.prevent="reset">
                        <i class="fas fa-times"></i>
                    </button>
                </template>
            </span>
        </div>
        <div class="c-input--presigned-upload__body">
            <label class="c-label--image-upload">
                <span class="c-icon--image-upload material-symbols-outlined">upload_file</span>
                画像アップロード
                <input
                    name="image-upload"
                    class="u-hidden"
                    type="file"
                    ref="fileInputRef"
                    @change="handleFileSelect" accept="image/jpeg, image/png"
                >
            </label>
            <template v-if="recommendSizeText">
                <span class="c-message--recommend-size u-margin-l-1p">
                    {{ recommendSizeText }}
                </span>
            </template>
        </div>
        <div v-if="showPreview">
            <img :src="previewUrl" alt="Uploaded Image">
        </div>
    </div>
</template>
