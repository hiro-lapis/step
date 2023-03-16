<script setup lang="ts">
import { inject, onMounted, provide, ref, reactive, readonly } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import CategoryBadge from '../../Atoms/CategoryBadge.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import TextInput from '../../Atoms/TextInput.vue'
import { Repositories } from '../../../apis/repositoryFactory'

// utilities
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>("$repositories")!
const router = useRouter()
const route = useRoute()

// data
const isEdit = ref(false)
type CreateData = {
    name: string
    category_id: number
    achievement_time_type_id: number
    sub_steps: Array<{name: string, detail: string, sort_number?: number}>
}
const createData = reactive<CreateData>({
    name: '',
    category_id: 0,
    achievement_time_type_id: 0,
    sub_steps : [],
})

const categorySelect = ref<InstanceType<typeof CategorySelectBox>>()

// computed
// watch
// methods
const initialSubStep = { name: '', detail: '', }
const addSubStep = () => createData.sub_steps.push(Object.assign({}, initialSubStep))

const create = async () => {
    if (requestStore.isLoading) return
    categorySelect.value?.validate()

    requestStore.setLoading(true)
    await $repositories.step.store(createData).then((response) => {
        messageStore.setMessage('ステップが登録されました')
        setTimeout(() => {
            router.push({ name: 'steps-list' })
        }, 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
const subStepLabel = (index: number) => {
    return `サブステップ${(index + 1)}`
}

const init = () => {
    // 編集画面か判定
    isEdit.value = route.name === 'steps-edit'
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
                            <h2 class="c-title">新規作成</h2>
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
                            <template v-for="(subStep, index) in createData.sub_steps">
                                <div class="p-form__element">
                                    <div class="p-form__element">
                                        <TextInput
                                            v-model:value="subStep.name"
                                            :label="subStepLabel(index)"
                                        />
                                    </div>
                                    <div class="p-form__element">
                                        <TextInput
                                            v-model:value="subStep.detail"
                                            label="詳細"
                                        />
                                    </div>
                                </div>
                            </template>
                            <!-- 子ステップ追加 -->
                            <div class="p-form__bottom">
                            <template v-if="!isEdit">
                                <button
                                    @click="addSubStep"
                                    class="c-btn c-btn--middle c-btn--add-sub-step"
                                >
                                    子ステップ追加
                                </button>
                                <button
                                    @click="create"
                                    class="c-btn c-btn--middle c-btn--create"
                                >
                                    登録
                                </button>
                            </template>
                        </div>
                        </div>
                    </div>
                    <!-- end:p-form__container -->
                    <div class="p-preview">
                        <div class="p-preview__container">
                            <div class="p-preview__head">
                                <h1 class="c-title">{{ createData.name }}</h1>
                                <div class="step-detail__info">
                                    <CategoryBadge v-if="createData.category_id" :id="createData.category_id" />
                                </div>
                            </div>
                            <div class="p-preview__body">
                                <div class="p-preview__sub-step">
                                    <template v-for="subStep in createData.sub_steps">
                                        <div>{{ subStep.name }}</div>
                                        <div>{{ subStep.detail }}</div>
                                    </template>
                                </div>
                            </div>
                            <div class="step-detail__body">
                            </div>
                        </div>
                    </div>
                </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
// TODO: アルファベット順に並べ替え
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/layout/_container.scss";

.p-form { // 編集フォームとプレビューのラッパー
    display: flex;
    justify-content: center;
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
        .c-btn:nth-child(2) {
            margin-top: 20px;
        }
    }
}

.p-preview { // プレビュー
    width: 100%;
    padding: 20px 40px;
    box-sizing: border-box;
    text-align: center;
    @include pc() {
        width: 600px;
        box-shadow: 0 0 8px #ccc;
    }
}

@import url('https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@400&family=Montserrat+Subrayada&display=swap');
.step-detail {
    font-family: 'M PLUS 1p', sans-serif;
    font-weight: 400;
}
</style>
