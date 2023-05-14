<script setup lang="ts">
import { computed, inject, onMounted, reactive, ref, watch } from 'vue'
import { User } from '../../../types/User'
import { useUserStore, useRequestStore, useMessageInfoStore } from '../../../store/globalStore'
import { Repositories } from '../../../apis/repositoryFactory'
import PasswordModal from '../../Molecules/PasswordModal.vue'
import TextInput from '../../Atoms/TextInput.vue'
import TextareaInput from '../../Atoms/TextareaInput.vue'
import UploadUserImage from '../../Atoms/UploadUserImage.vue'
import SingleCheckbox from '../../Atoms/SingleCheckbox.vue'
import { repositoryKey } from '../../../types/common/Injection'
import { useValidation } from '../../../composables/validation'

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
const uploadImage = ref<File|null>(null)
const fileFlg = ref(false)
const errorMessage = reactive({
    name: '',
    email: '',
    profile: '',
})
// computed
const isInvalid = computed(() => {
    return Object.values(errorMessage).some(value => value !== '')
})

// methods
const validate = useValidation()
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
// 入力情報の監視
watch ([() => user.name],
    ([]) => {
        if (requestStore.isLoading) return
        let result: true | string
        result = validate.required(user.name)
        errorMessage.name = result === true ? '' : result
    }
)
watch ([() => user.email],
    ([]) => {
        if (requestStore.isLoading) return
        let result: true | string
        // 入力必須
        result = validate.required(user.email)
        errorMessage.email = result === true ? '' : result
        if (result !== true) return
        // メールアドレス形式
        let result2: RegExpMatchArray | string
        result2 = validate.email(user.email)
        errorMessage.email = typeof result2 !== 'string' ? '' : result2
    }
)
watch ([() => user.profile],
    ([]) => {
        if (requestStore.isLoading) return
        let result: true | string
        result = validate.max(user.profile, 1000)
        errorMessage.profile = result === true ? '' : result
    }
)
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
                v-model:value="user.name"
                :errorMessage="errorMessage.name"
                label="ユーザー名"
                formId="name"
                placeHoler="アカウント名"
            />
        </div>
        <div class="c-multi-page--profile__element">
            <TextInput
                v-model:value="user.email"
                :errorMessage="errorMessage.email"
                label="メールアドレス"
                formId="email"
                placeHoler="メールアドレス"
            />
        </div>
        <div class="c-multi-page--profile__element">
            <TextareaInput
                v-model:value="user.profile"
                :errorMessage="errorMessage.profile"
                label="プロフィール"
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
            <button :disabled="isInvalid" @click="update" class="c-btn--update-profile">
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
