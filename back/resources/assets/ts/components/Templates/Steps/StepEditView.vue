<script setup lang="ts">
import { computed, inject, onMounted, provide, ref, reactive, readonly } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import TextInput from '../../Atoms/TextInput.vue'
import TextareaInput from '../../Atoms/TextareaInput.vue'
import PresignedUploadInput from '../../Atoms/PresignedUploadInput.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'
import { repositoryKey } from '../../../types/common/Injection'
import draggable from 'vuedraggable'

// utilities
const requestStore = useRequestStore()
const userStore = useUserStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>(repositoryKey)!
const router = useRouter()
const route = useRoute()
// emits
interface Emits {
    (e: 'update:previewUrl', previewUrl: string): void
}
const emit = defineEmits<Emits>()

// data
const isEdit = ref(false)
const drag = ref(false)
const createData = reactive<Step>({
    id: 0,
    name: '',
    summary: '',
    image_url: '',
    category_id: 0,
    achievement_time_type_id: 0,
    sub_steps: [{ name: '', detail: '', sort_number: 1}],
})
const categorySelect = ref<InstanceType<typeof CategorySelectBox>>()

// computed
const pageTitle = computed(() => isEdit.value ? 'ステップ更新' : '新規作成')
// methods
const initialSubStep = { name: '', detail: '', }
// const addSubStep = () => createData.sub_steps.push(Object.assign({}, initialSubStep))
const addSubStep = () => {
    const nextSortNumber = createData.sub_steps.length + 1
    createData.sub_steps.push(Object.assign({ sort_number: nextSortNumber }, initialSubStep))
}

// 編集中のサブステップ情報を削除
const popSubStep = (index: number) => {
    if (createData.sub_steps.length === 1) return
    createData.sub_steps.splice(index, 1)
}
// ステップ新規作成
const create = async () => {
    if (requestStore.isLoading) return
    categorySelect.value?.validate()

    requestStore.setLoading(true)
    await $repositories.step.store(createData).then(() => {
        messageStore.setMessage('ステップが登録されました')
        setTimeout(() => {
            router.push({ name: 'steps-list' })
        }, 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
// ステップ更新
const update = async () => {
    if (requestStore.isLoading) return
    categorySelect.value?.validate()
    requestStore.setLoading(true)
    await $repositories.step.update(createData).then((response) => {
        messageStore.setMessage('ステップが更新されました')
        // 詳細画面へ遷移
        setTimeout(() => {
            router.push({ name: 'steps-show', params: { id: response.data.step.id! } })
        }, 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
// ステップ編集情報取得
const getStep = () => {
    requestStore.setLoading(true)
        // 編集画面の場合、ステップ情報を取得
        $repositories.step.find(Number(route.params.id))
            .then(res => {
                const step = res.data
                createData.id = step.id!
                createData.name = step.name
                createData.image_url = step.image_url
                createData.summary = step.summary
                createData.category_id = step.category_id
                createData.achievement_time_type_id = step.achievement_time_type_id
                createData.sub_steps = step.sub_steps
                requestStore.setLoading(false)
                // ログインユーザーとステップのユーザーが異なる場合、ステップ一覧にリダイレクト
                if ('is_writer' in step && !step.is_writer) {
                    messageStore.setErrorMessage('他のユーザーのステップは編集できません')
                    setTimeout(() => {
                        router.push({ name: 'steps-list' })
                    }, 3000)
                }
            })
}
// ステップ編集入力補完
const completion = async (subStepIndex: number, title: string, text: string) => {
    if (!title.trim() || !text.trim()) {
        messageStore.setErrorMessage(subStepLabel(subStepIndex) + 'のタイトルとテキストを入力してください')
        return
    }
    if (title.length > 255 || text.length > 255) {
        messageStore.setErrorMessage('タイトル,本文それぞれ100文字以内で入力してください')
        return
    }
    if (!userStore.existsRemainCount) {
        messageStore.setErrorMessage('利用可能回数が残っていません')
        return
    }
    if (!confirm("サジェスト機能を利用しますか？") && !userStore.skipApiConfirm) return

    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.chatGpt.completion(title, text).then(res => {
        // 補完文字列をサブステップの詳細に追加
        createData.sub_steps[subStepIndex].detail += res.data.message
        messageStore.setMessage("サジェスト文が入力欄に入力されました \n 本日の残り利用可能回数: " + res.data.remain_count + "回")
        if (res.data.remain_count !== -1) {
            userStore.setRemainCount(res.data.remain_count)
        }
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
// サブステップのタイトルを返す
const subStepLabel = (index: number): string => {
    return `サブステップ タイトル${(index + 1).toString()}`
}
// 入力補完の説明を表示
const displayComplectionExplain = () => {
    messageStore.setMessage('*chat GPTサジェストを利用したい方は、サブステップのタイトルと詳細の概要を書いてアイコンをクリック、またはshift + Enter を押してください')
}
// 初期化
const init = () => {
    // 編集画面か判定
    isEdit.value = route.name === 'steps-edit'
    if (isEdit.value) {
        getStep()
    }
    // カテゴリー情報を取得し各種コンポーネントで使用
    $repositories.common.category()
        .then(res => {
            const categories = res.data
            provide('categories', readonly(categories))
        })
}
onMounted(() => {
    init()
})
</script>

<template>
    <BaseView className="p-container--steps-form">
        <template v-slot:content>
                <div class="p-step-edit-form">
                    <!-- start:p-step-edit-form__container -->
                    <div class="p-step-edit-form__editor">
                        <div class="p-step-edit-form__head">
                            <h2 class="c-title--step-edit">{{ pageTitle }}</h2>
                        </div>
                        <div class="p-step-edit-form__body">
                            <!-- ステップ名 -->
                            <div class="p-step-edit-form__element">
                                <PresignedUploadInput
                                    :previewMode="false"
                                    v-model:previewUrl="createData.image_url"
                                    label="サムネイル"
                                 />
                            </div>
                            <!-- ステップ名 -->
                            <div class="p-step-edit-form__element">
                                <TextInput
                                    v-model:value="createData.name"
                                    errorKey="name"
                                    label="タイトル"
                                    formId="step-name"
                                    placeHolder="入力必須"
                                    required
                                />
                            </div>
                            <!-- カテゴリー -->
                            <div class="p-step-edit-form__element">
                                <CategorySelectBox
                                    ref="categorySelect"
                                    v-model:value="createData.category_id"
                                    label="カテゴリー"
                                    required
                                />
                            </div>
                            <!-- 達成目安時間 -->
                            <div class="p-step-edit-form__element">
                                <AchievementTimeTypeSelectBox
                                    v-model:value="createData.achievement_time_type_id"
                                    label="達成目安時間"
                                    required
                                />
                            </div>
                            <!-- 概要 -->
                            <div class="p-step-edit-form__element">
                                <TextareaInput
                                    v-model:value="createData.summary"
                                    :errorMessage="''"
                                    height="100"
                                    label="概要"
                                />
                            </div>
                            <!-- chat GPT入力補完について -->
                            <div class="p-step-edit-form__explain-text">
                                <span class="c-title--explain-completion" @click="displayComplectionExplain">＊chat GPTサジェストについて</span>
                            </div>
                            <!-- サブステップ -->
                            <!-- v-model > 配列で、並び替えはしない。List＞並び替えをする.-->
                            <!-- ghost-classでドラッグ中のスタイル指定（sortable.jsのクラスをつかえる） -->
                            <div class="p-step-edit-form__substep-form__list">
                                <draggable :list="createData.sub_steps" item-key="sort_number" :animation=300 tag="div" ghost-class="blue-background-class">
                                    <!-- slotで囲う要素は１つにまとめる -->
                                    <template v-slot:item="{ element: subStep, index}">
                                        <div class="p-step-edit-form__substep-form-item">
                                            <span class="p-step-edit-form__substep-form-handle">
                                                <span class="c-icon--line material-symbols-outlined">menu</span>
                                            </span>
                                            <div class="p-step-edit-form__element">
                                                <TextInput
                                                    @key-down:shift-enter="completion(index, subStep.name, subStep.detail)"
                                                    v-model:value="subStep.name"
                                                    :label="subStepLabel(index)"
                                                >
                                                <template v-slot:asideLabel>
                                                    <i v-if="index !== 0" @click="popSubStep(index)" class="c-icon--delete fas fa-times-circle"></i>
                                                </template>
                                                </TextInput>
                                            </div>
                                            <div class="p-step-edit-form__element">
                                                <TextareaInput
                                                    @key-down:shift-enter="completion(index, subStep.name, subStep.detail)"
                                                    v-model:value="subStep.detail"
                                                    :errorMessage="''"
                                                    height="200"
                                                    :label="'詳細' + (index as number +1).toString()"
                                                    :formId="'substep-' + index.toString()"
                                                >
                                                <template v-slot:labelAside>
                                                    <span
                                                        @click="completion(index, subStep.name, subStep.detail)"
                                                    >
                                                        <img
                                                            src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/logos/chat-GPT-logo.png"
                                                            alt="chat-GPTロゴ"
                                                            class="c-img--chat-gpt"
                                                        >
                                                    </span>
                                                </template>
                                                </TextareaInput>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                            <!-- サブステップ追加 -->
                            <div class="p-step-edit-form__bottom">
                                <button
                                    @click="addSubStep"
                                    class="c-btn c-btn--middle c-btn--add-sub-step"
                                >
                                    サブステップ追加
                                </button>
                            <template v-if="!isEdit">
                                <button
                                    @click="create"
                                    class="c-btn c-btn--middle c-btn--create"
                                >
                                    登録
                                </button>
                            </template>
                            <template v-else>
                                <button
                                @click="update"
                                class="c-btn c-btn--middle c-btn--update"
                                >
                                更新
                            </button>
                            </template>
                        </div>
                        </div>
                    </div>
                    <div class="p-preview">
                        <StepPreview :step="createData" />
                    </div>
                </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
.material-symbols-outlined {
  font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 48
}
</style>
