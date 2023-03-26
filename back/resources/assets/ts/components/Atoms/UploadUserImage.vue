<script setup lang="ts">
import { computed, ref, PropType } from 'vue'

/**
 *単一画像ファイルアップロードコンポーネント
 */
// props
const props = defineProps({
    type: { required: true, type: String, },
    imageUrl: { required: true, type: [String, null] as PropType<string | null>, },
})


interface Emits {
    (e: 'update:upload-image', file: File): void
}
// 親画面へアップロードする画像ファイルを発信
const emit = defineEmits<Emits>()

// data
const message = ref('')
const file = ref<File|null>(null)
const previewMode = ref(true)
const confirmedImage = ref<string | ArrayBuffer | null>('')

// computed
const isNotUpload = computed(() => {
    return confirmedImage.value === '' || confirmedImage.value === null
})
const sampleImage = computed(() => {
    return 'https://laravel8-inspiration.s3-ap-northeast-1.amazonaws.com/no_user_icon_img.png'
})
// 状態に応じた画面表示する画像のURL
const imageUrl = computed(() => {
    // フォームで画像を選択済の場合はアップロード画像のURL
    if (confirmedImage.value !== '') {
        return confirmedImage.value
    }
    // フォームで画像を未選択の場合はユーザー設定の画像URL
    if (props.imageUrl !== '') {
        return props.imageUrl
    }
    // ユーザー設定の画像がない時はサンプル画像のURL
    return sampleImage.value
})

// methods
const confirmImage = (e: Event) => {
    file.value = ((e.target as HTMLInputElement).files as FileList)[0]

    if (!file.value.type.match('image.*')) {
        alert('画像ファイルを選択して下さい')
        // confirmedImage.value = ''
        return
    }
    createImage(file.value)
}

const createImage = (file: File) => {
    let reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (e: ProgressEvent) => {
        confirmedImage.value = reader.result
    }
    emit('update:upload-image', file)
}
</script>

<template>
    <div>
        <div class="c-upload-image__box">
            <input
                class="u-hidden"
                id="upload-image"
                type="file"
                @change="confirmImage"
                v-if="previewMode"
            />
            <label
                :class="{ 'c-upload-image--sample': isNotUpload }"
                for="upload-image"
            >
                <div class="c-upload-image--user">
                    <img class="c-img--upload-user-icon-preview" :src="(imageUrl as string)" />
                </div>
            </label>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.c-upload-image { // アップロード画像外枠
    width: 200px;
    &--user {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        box-sizing: border-box;
        overflow: hidden;
        background-position: center center;
    }
    &--sample {
        background-size: contain; // 縦横比を崩さない範囲で最大
        background-position: center; // 画像の配置
        background-repeat: no-repeat; // 画像を繰り返さない
        // background-image: url("/images/no-image.png");
    }
}
</style>
