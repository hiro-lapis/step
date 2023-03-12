<script setup lang="ts">
import { inject, onMounted, ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'
import TextInput from '../../Atoms/TextInput.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import { Repositories } from '../../../apis/repositoryFactory'

// utilities
const requestStore = useRequestStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>("$repositories")!
const router = useRouter()
const route = useRoute()

// data
const isEdit = ref(false)
const createData = reactive({
    name: '',
    category_id: 0,
    achievement_time_type_id: 0,
})
const categorySelect = ref<InstanceType<typeof CategorySelectBox>>()

// computed
// watch
// methods

const create = async () => {
    console.log('requestStore.isLoading')
    console.log(requestStore.isLoading)

    if (requestStore.isLoading) return
    categorySelect.value?.validate()

    requestStore.setLoading(true)
    await $repositories.step.store(createData).then((response) => {
        messageStore.setMessage('ステップが登録されました')
        router.push({ name: 'steps-list' })
    }).finally(() => {
        requestStore.setLoading(false)
    })
}

const init = () => {
    isEdit.value = route.name === 'steps-edit'
}
onMounted(() => {
    init()
})
</script>

<template>
    <BaseView>
        <template v-slot:content>
                <div class="p-form">
                    <div class="p-form__container">
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
                            <div class="p-form__bottom">
                            <template v-if="!isEdit">
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
                </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
// TODO: アルファベット順に並べ替え
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/layout/_container.scss";

.p-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.p-form {
    &__container { // 入力フォーム
        margin: 0 auto;
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
</style>
