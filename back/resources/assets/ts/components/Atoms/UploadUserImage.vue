<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps({
    // アップロードタイプ(user/step)
    type: { required: true, type: String, },
    userImage: { required: true, type: String, },
})

// data
const message = ref('')
const file = ref<File|null>(null)
const title = ref('')
const view = ref(true)
const images = ref([])
const confirmedImage = ref<string | ArrayBuffer | null>('')

// methods

const confirmImage = (e: Event) => {
    message.value = ''
    file.value = ((e.target as HTMLInputElement).files as FileList)[0]

    if (!file.value.type.match('image.*')) {
        message.value = '画像ファイルを選択して下さい';
        confirmedImage.value = '';
        return;
    }
    createImage(file.value);
}

const createImage = (file: File) => {
    let reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (e: ProgressEvent) => {
        confirmedImage.value = reader.result
        // confirmedImage.value = e.target?.result
    }
}

const setConfirmedImage = (url: string) => {
    confirmedImage.value = url
}
// computed

// アップロード先URLの指定
const uploadUrl = computed(() => {
    return props.type === 'user' ? '/api/images/user' : '/api/images/idea'
})
const isUserType = computed(() => {
        return props.type === 'user'
})
const isNotUpload = computed(() => {
    return confirmedImage.value === ''
})
const sampleImage = computed(() => {
    return 'https://laravel8-inspiration.s3-ap-northeast-1.amazonaws.com/no_user_icon_img.png'
})
const imageUrl = computed(() => {
    // フォームで画像を選択済の場合はアップロード画像のURLを返す
    if (confirmedImage.value !== '') {
        return confirmedImage.value;
    }
    if (props.userImage !== '') {
        return props.userImage
    }
    // ユーザー設定の画像がない時はサンプル画像のURLを返す
    return sampleImage.value;
})
</script>

<template>
    <div>
        <div class="c-upload-image__box">
            <input
                class="u-hidden"
                id="user-image"
                type="file"
                @change="confirmImage"
                v-if="view"
            />
            <label
                :class="{ 'c-upload-image--sample': isNotUpload }"
                for="user-image"
            >
                <div
                    :class="[
                        isUserType
                            ? 'c-upload-image--user'
                            : 'c-upload-image--idea'
                    ]"
                >
                    <img class="c-img" :src="(imageUrl as string)" />
                </div>
            </label>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.c-upload-image {
    width: 200px;
    &--idea {
        margin: 0 auto;
        width: 200px;
        height: 200px;
        overflow: hidden;
        border-radius: 3px;
        background-position: center center;
    }
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
