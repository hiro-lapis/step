<script setup lang="ts">
import { computed, inject, onMounted, provide, ref, reactive, readonly } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import StepPreview from '../../Organisms/Steps/StepPreview.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import TextInput from '../../Atoms/TextInput.vue'
import TextareaInput from '../../Atoms/TextareaInput.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'
import { repositoryKey } from '../../../types/common/Injection'


// utilities
const requestStore = useRequestStore()
const userStore = useUserStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>(repositoryKey)!
const router = useRouter()
const route = useRoute()

// data
const isEdit = ref(false)
const createData = reactive<Step>({
    id: 0,
    name: '',
    summary: '',
    category_id: 0,
    achievement_time_type_id: 0,
    sub_steps: [{ name: '', detail: '', }],
})

const categorySelect = ref<InstanceType<typeof CategorySelectBox>>()

// computed
const pageTitle = computed(() => isEdit.value ? 'ステップ更新' : '新規作成')
// watch
// methods
const initialSubStep = { name: '', detail: '', }
const addSubStep = () => createData.sub_steps.push(Object.assign({}, initialSubStep))

// 編集中のサブステップ情報を削除
const popSubStep = (index: number) => {
    if (createData.sub_steps.length === 1) return
    createData.sub_steps.splice(index, 1)
}
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
const getStep = () => {
    requestStore.setLoading(true)
        // 編集画面の場合、ステップ情報を取得
        $repositories.step.find(Number(route.params.id))
            .then(res => {
                const step = res.data
                createData.id = step.id!
                createData.name = step.name
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

const completion = async (subStepIndex: number, title: string, text: string) => {
    if (!title.trim() || !text.trim()) {
        messageStore.setErrorMessage(subStepLabel(subStepIndex) + 'のタイトルとテキストを入力してください')
        return
    }
    if (title.length > 255 || text.length > 255) {
        messageStore.setErrorMessage('タイトル,本文それぞれ100文字以内で入力してください')
        return
    }
    if (userStore.existsRemainCount) {
        messageStore.setErrorMessage('利用可能回数が残っていません')
        return
    }
    if (!confirm("サジェスト機能を利用しますか？") && !userStore.skipApiConfirm) return

    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.chatGpt.completion(title, text).then(res => {
        // 補完文字列をサブステップの詳細に追加
        createData.sub_steps[subStepIndex].detail += res.data.message
        messageStore.setMessage("サジェスト分が入力欄に入力されました \n 本日の残り利用可能回数: " + res.data.remain_count + "回")
        if (res.data.remain_count !== -1) {
            userStore.setRemainCount(res.data.remain_count)
        }
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
const subStepLabel = (index: number): string => {
    return `サブステップ タイトル${(index + 1).toString()}`
}
const displayComplectionExplain = () => {
    messageStore.setMessage('*chat GPTサジェストを利用したい方は、サブステップのタイトルと詳細の概要を書いてアイコンをクリック、またはshift + Enter を押してください')
}
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
                <div class="p-form">
                    <!-- start:p-form__container -->
                    <div class="p-form__editor">
                        <div class="p-form__head">
                            <h2 class="c-title--step-edit">{{ pageTitle }}</h2>
                        </div>
                        <div class="p-form__body">
                            <!-- ステップ名 -->
                            <div class="p-form__element">
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
                            <div class="p-form__element">
                                <CategorySelectBox
                                    ref="categorySelect"
                                    v-model:value="createData.category_id"
                                    label="カテゴリー"
                                    required
                                />
                            </div>
                            <!-- 達成目安時間 -->
                            <div class="p-form__element">
                                <AchievementTimeTypeSelectBox
                                    v-model:value="createData.achievement_time_type_id"
                                    label="達成目安時間"
                                    required
                                />
                            </div>
                            <!-- 概要 -->
                            <div class="p-form__element">
                                <TextareaInput
                                    v-model:value="createData.summary"
                                    height="100"
                                    label="概要"
                                />
                            </div>
                            <!-- chat GPT入力補完について -->
                            <div class="p-form__explain-text">
                                <span class="c-title--explain-completion" @click="displayComplectionExplain">＊chat GPTサジェストについて</span>
                            </div>
                            <template :key="index" v-for="(subStep, index) in createData.sub_steps">
                                <div class="p-form__element">
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
                                <div class="p-form__element">
                                    <TextareaInput
                                        @key-down:shift-enter="completion(index, subStep.name, subStep.detail)"
                                        v-model:value="subStep.detail"
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
                            </template>
                            <!-- サブステップ追加 -->
                            <div class="p-form__bottom">
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
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/layout/_container.scss";

.p-form { // 編集フォームとプレビューのラッパー
    display: flex;
    justify-content: center;
    flex-direction: column;
    @include pc() {
        flex-direction: row;
    }
    &__editor { // 編集フォーム
        // margin: 0 auto;
        width: 100%;
        padding: 20px 40px;
        box-sizing: border-box;
        text-align: center;
        @include pc() {
            width: 600px;
            box-shadow: 0 0 8px #ccc;
        }
    }
    &__head { // 入力フォームヘッダ
        margin-bottom: 15px;
        @include pc() {
            margin-bottom: 30px;
        }
        text-align: center;
        font-size: 16px;
    }
    &__body { // 入力フォーム入力UIパート
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
    }
    &__element { // 各種入力UIラッパー
        width: 100%;
        margin-bottom: 15px;
    }
    &__bottom { // 登録更新ボタンラッパー
        // 新規作成(ボタンが2つ存在しない)の時のため、2番目のボタンにマージンを設定
        width: 100%;
        .c-btn:nth-child(2) {
            margin-top: 20px;
        }
    }
    &__explain-text {
        text-align: left;
        display: inline-block;
        width: 100%;
        margin-bottom: 20px;
    }
}

.p-preview { // プレビュー
    width: 100%;
    display: none;
    box-sizing: border-box;
    text-align: center;
    @include pc() {
        display: flex;
        width: 600px;
        box-shadow: 0 0 8px #fff;
    }
}

@import url('https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@400&family=Montserrat+Subrayada&display=swap');
.step-detail {
    font-family: 'M PLUS 1p', sans-serif;
    font-weight: 400;
}
</style>
