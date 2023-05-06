<script setup lang="ts">
import { inject, onMounted, reactive, ref } from 'vue'
import { User } from '../../../types/User'
import { useUserStore, useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import PasswordModal from '../../Molecules/PasswordModal.vue'
import TextInput from '../../Atoms/TextInput.vue'
import TextareaInput from '../../Atoms/TextareaInput.vue'
import UploadUserImage from '../../Atoms/UploadUserImage.vue'
import SingleCheckbox from '../../Atoms/SingleCheckbox.vue'
import { repositoryKey } from '../../../types/common/Injection'

// utilities
const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const userStore = useUserStore()
const $repositories = inject<Repositories>(repositoryKey)!

// data
const user = reactive<User>({
    id: -1,
    name:'',
    image_url:'',
    email: '',
    profile: '',
    skip_api_confirm: false,
})
const passwordUpdateMode = ref(false)
// const uploadImage = ref<InstanceType<typeof UploadUserImage>>()
const uploadImage = ref<File|null>(null)
const fileFlg = ref(false)

// methods
const showPasswordUpdateModal = () => passwordUpdateMode.value = !passwordUpdateMode.value

const fetchData = () => {
    $repositories.mypage.user()
        .then(response => {
            user.id = response.data.user.id
            user.name = response.data.user.name
            user.email = response.data.user.email
            user.image_url = response.data.user.image_url
            user.profile = response.data.user.profile
            user.skip_api_confirm = response.data.user.skip_api_confirm
        })
}

const update = () => {
    if (requestStore.isLoading) return
    const params = {
        name: user.name,
        email: user.email,
        profile: user.profile,
        skip_api_confirm: user.skip_api_confirm!,
    }

    let file: File|null = null
    if (!!uploadImage.value && fileFlg.value) {
        file = uploadImage.value
    }
    requestStore.setLoading(true)
    $repositories.mypage.update(params, file)
        .then(response => {
            messageStore.setMessage(response.data.message)
            userStore.setUser(response.data.user)
        })
        .finally(() => {
            requestStore.setLoading(false)
        })
}
const setDataByStore = () => {
    user.id = userStore.user.id
    user.name = userStore.user.name
    user.email = userStore.user.email
    user.image_url = userStore.user.image_url
    user.profile = userStore.user.profile
}
const setUpdloadImage = (e) => {
    uploadImage.value = e
    fileFlg.value = true
}
onMounted(() => {
    // 初期情報としてstoreのユーザー情報をページの状態に設定
    setDataByStore()
    // APIでユーザー情報を取得
    fetchData()
})
</script>

<template>
    <div class="c-multi-page--profile">
        <PasswordModal
            @close="passwordUpdateMode = false"
            title="パスワード変更"
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
                @update:upload-image="setUpdloadImage"
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
        <div class="c-multi-page--profile__element">
            <TextareaInput
                label="プロフィール"
                v-model:value="user.profile"
                formId="profile"
                placeHoler="自己紹介文"
                counter
                height="200"
            />
        </div>
        <div class="c-multi-page--profile__element">
            <p class="u-margin-b-1p">残りAI利用可能回数:{{ userStore.remainCount }}</p>
            <p>1日100回まで利用できます</p>
        </div>
        <div class="c-multi-page--profile__element">
            <SingleCheckbox
                label="API利用確認スキップ"
                v-model:value="user.skip_api_confirm"
                formId="skip_api_confirm"
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
