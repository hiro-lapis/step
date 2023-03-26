<script setup lang="ts">
import { inject, onMounted, reactive, ref } from 'vue'
import { User } from '../../../types/User'
import { useRouter } from 'vue-router'
import { useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import TextInput from '../../Atoms/TextInput.vue'
import UploadUserImage from '../../Atoms/UploadUserImage.vue'

// utilities
const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const $repositories = inject<Repositories>("$repositories")!

// data
const user = reactive<User>({
    id: -1,
    name:'',
    image_url:'',
    email: '',
    profile: '',
})
const passwordUpdateMode = ref(false)
// const uploadImage = ref<InstanceType<typeof UploadUserImage>>()
const uploadImage = ref<File|null>(null)

// emits
// computed
// watch
// methods
const showPasswordUpdateModal = () => passwordUpdateMode.value = !passwordUpdateMode.value

const fetchData = () => {
    $repositories.mypage.user()
        .then(response => {
            user.id = response.data.user.id
            user.name = response.data.user.name
            user.email = response.data.user.email
            user.image_url = response.data.user.image_url
        })
}

const update = () => {
    if (requestStore.isLoading) return
    /**
     * パラメータを設定
     * 画像アップロードとフォームデータを同時送信する場合、下記に注意
     * ・FormDataオブジェクトを使う(ファイルアップロード時の作法)
     * ・ファイルは配列キーにして渡す(文字列にキャストされるのを防ぐ)
     */
    let params = {
        name: user.name,
        email: user.email,
    }

    // params.append("name", user.name!)
    // params.append("email", user.email!)
    let file: File|null = null
    if (!!uploadImage.value) {
        file = uploadImage.value
    }
    requestStore.setLoading(true)
    $repositories.mypage.update(params, file)
        .then(response => {
            messageStore.setMessage(response.data.message)
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}
onMounted(() => {
    fetchData()
})
</script>

<template>
    <div class="c-multi-page--profile">
        <PassWordModal
            @close="passwordUpdateMode = false"
            name="パスワード変更"
            v-show="passwordUpdateMode"
        />
        <div class="c-multi-page--profile__image-input">
            <div class="c-multi-page--profile__element">
                <label for="user-image" class="c-label">
                    ユーザーアイコン
                </label>
            </div>
            <UploadUserImage
                ref="uploadImage"
                type="user"
                :imageUrl="user.image_url "
                @update:upload-image="e => uploadImage = e"
            />
        </div>
        <div class="c-multi-page--profile__element">
            <TextInput
                label="ユーザー名"
                v-model:value="user.name"
                formId="name"
                placeHoler="アカウント名"
            />
        </div>
        <div class="c-multi-page--profile__element">
            <TextInput
                label="メールアドレス"
                v-model:value="user.email"
                formId="email"
                placeHoler="メールアドレス"
            />
        </div>
        <div class="c-multi-page--profile__btn-container">
            <button @click="update" class="c-btn--update-profile">
                更新
            </button>
            <button
                @click="showPasswordUpdateModal"
                class="c-btn--update-password">
                パスワード変更
            </button>
        </div>
    </div>
</template>
