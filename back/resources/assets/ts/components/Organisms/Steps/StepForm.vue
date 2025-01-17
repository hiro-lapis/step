<script setup lang="ts">
import { computed, inject, onMounted, provide, ref, reactive, readonly, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useMessageInfoStore, useRequestStore, useUserStore } from '../../../store/globalStore'
import StepPreview from './StepPreview.vue'
import AchievementTimeTypeSelectBox from '../../Atoms/AchievementTimeTypeSelectBox.vue'
import CategorySelectBox from '../../Atoms/CategorySelectBox.vue'
import TextInput from '../../Atoms/TextInput.vue'
import NumberInput from '../../Atoms/NumberInput.vue'
import BorderLine from '../../Atoms/BorderLine.vue'
import TextareaInput from '../../Atoms/TextareaInput.vue'
import PresignedUploadInput from '../../Atoms/PresignedUploadInput.vue'
import CommonModal from '../../Atoms/CommonModal.vue'
import { Repositories } from '../../../apis/repositoryFactory'
import { Step } from '../../../types/Step'
import { useValidation } from '../../../composables/validation'
import { useAchievementTimeTypeCheck } from '../../../composables/achievementTimeTypeCheck'
import { repositoryKey } from '../../../types/common/Injection'
import draggable from 'vuedraggable'
import VueCropper from 'vue-cropperjs'
import 'cropperjs/dist/cropper.css'

// utilities
const requestStore = useRequestStore()
const userStore = useUserStore()
const messageStore = useMessageInfoStore()
const $repositories = inject<Repositories>(repositoryKey)!
const router = useRouter()
const route = useRoute()
const timeType = useAchievementTimeTypeCheck()

// props
const props = defineProps({
    mode: { required: true, type: String, validator: (v: string) => ['create', 'edit'].includes(v) },
    stepId: { required: false, type: Number, default: 0 },
})
// emits
interface Emits {
    (e: 'update:previewUrl', previewUrl: string): void
}
const emit = defineEmits<Emits>()

// data
const DEFULAT_IMAGE_URL = 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/step-card-default.png'
const createData = reactive<Step>({
    id: 0,
    name: '',
    summary: '',
    image_url: DEFULAT_IMAGE_URL,
    category_id: 0,
    achievement_time_type_id: 1,
    time_count: 1,
    sub_steps: [{ name: '', detail: '', sort_number: 1}],
})
const errorMessage = reactive({
    name: '',
    summary: '',
    image_url: '',
    category_id: '',
    achievement_time_type_id: '',
    time_count: '',
    sub_steps: '',
    // sub_steps: [ { name: '', detail: ''} ],
})
const validSubStepCounts = ref(0)
const partialValidSubStepCounts = ref(0)

const categorySelect = ref<InstanceType<typeof CategorySelectBox>>()
const helpMode = ref(false)
// computed
const isEdit = computed(() => props.mode === 'edit')
const pageTitle = computed(() => {
    if (isEdit.value) {
        return createData.is_active ? 'ステップ編集' : 'ステップ編集（下書き）'
    }
    return '新規作成'
})
const disableSubmit = computed(() => {
    const existError = Object.keys(errorMessage).every(element => {
        return errorMessage[element].length === 0
    })
    return !existError
})
const updateText = computed(() => createData.is_active ? '更新' : '更新して公開')
const showDraft = computed(() => !isEdit.value || createData.is_active === false)
const isInitialImageUrl = computed(() => createData.image_url === DEFULAT_IMAGE_URL || createData.image_url === '')
// methods
const addSubStep = () => {
    const initialSubStep = { name: '', detail: '', }
    const nextSortNumber = createData.sub_steps.length + 1
    createData.sub_steps.push(Object.assign({ sort_number: nextSortNumber }, initialSubStep))
}
const getValidTimeCountSpan = computed(() => timeType.getValidTimeCountSpan(createData.achievement_time_type_id))
const validSubStepCount = () => createData.sub_steps.filter(subStep => subStep.name.length > 0 && subStep.detail.length > 0).length
const partialValidSubStepCount = () => createData.sub_steps.filter(subStep => {
    return (subStep.name.length > 0 && subStep.detail.length === 0) || (subStep.name.length === 0 && subStep.detail.length > 0)
}).length
// ステップ新規作成
const create = async () => {
    if (requestStore.isLoading) return
    if (validSubStepCounts.value === 0) {
        if (!confirm('サブステップが1つも登録されていませんが、このまま公開しますか？\n＊下書きで保存して、サブステップを入力してから公開するのをおすすめします。')) {
            return
        }
    } else if (partialValidSubStepCounts.value > 0) {
        if (!confirm('一部、タイトルもしくは詳細が未入力のサブステップがあります。\nこのまま公開しますか？')) {
            return
        }
    }
    requestStore.setLoading(true)
    await $repositories.step.store(createData).then(() => {
        messageStore.setMessage('ステップが登録されました')
        setTimeout(() => router.push({ name: 'steps-list' }), 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
// ステップ更新
const update = async () => {
    if (requestStore.isLoading) return
    if (validSubStepCounts.value === 0) {
        if (!confirm('サブステップが1つも登録されていませんが、\nこのまま更新しますか？')) {
            return
        }
    } else if (partialValidSubStepCounts.value > 0) {
        if (!confirm('一部タイトルもしくは詳細が未入力のサブステップがあります。\nこのまま更新しますか？')) {
            return
        }
    }
    requestStore.setLoading(true)
    await $repositories.step.update(createData).then((response) => {
        const operate = !createData.is_active && response.data.step.is_active ? '公開' : '更新'
        messageStore.setMessage(`ステップが${operate}されました`)
        // 詳細画面へ遷移
        setTimeout(() => router.push({ name: 'steps-show', params: { id: response.data.step.id! } }), 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
const saveDraft = async () => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.step.saveDraft(createData).then(() => {
        messageStore.setMessage('ステップが下書き保存されました')
        setTimeout(() => router.push({ name: 'steps-list' }) , 3000)
    }).finally(() => {
        requestStore.setLoading(false)
    })
}
// ステップ編集情報取得
const getStep = () => {
    requestStore.setLoading(true)
        // 編集画面の場合、ステップ情報を取得
        $repositories.step.findEdit(Number(route.params.id))
            .then(res => {
                // 下書きを考慮して値が未設定の時は初期値を入れる
                const step = res.data
                createData.id = step.id!
                createData.name = step.name ?? ''
                createData.is_active = step.is_active
                createData.image_url = step.image_url
                createData.summary = step.summary
                createData.category_id = step.category_id ?? 0
                createData.achievement_time_type_id = step.achievement_time_type_id ?? 1
                createData.time_count = step.time_count ?? 1
                createData.sub_steps = step.sub_steps
                requestStore.setLoading(false)
                // 画像切り抜きコンポーネント用URL
                cropperUrl.value = step.image_url
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
//　ステップの入力状態を見てバリデーション。エラーメッセージの表示を切り替え
const validate = useValidation()
// ステップ名
watch(
    () => createData.name,
    (newData) => {
        let result: boolean|string
        result = validate.stepName(newData)
        errorMessage.name = result !== true ? result : ''
    }, { deep: true }
)
// ステップ概要
watch(
    () => createData.summary,
    (newData) => {
        let result: boolean|string
        result = validate.stepSummary(newData)
        errorMessage.name = result !== true ? result : ''
    }
)
// ステップカテゴリー
watch(
    () => createData.category_id,
    (newData) => {
        let result: boolean|string
        result = validate.selectRequired(newData)
        errorMessage.name = result !== true ? result : ''
    }
)
// 達成目安時間(単位)
watch(
    () => [createData.achievement_time_type_id ],
    ([newAchievementTimeType ]) => {
        const limitTimeCount = timeType.getLimitTimeCount(newAchievementTimeType)
        // 目安期間の単位を変えたことで、目安期間が上限を超えている場合、目安期間を1に戻す
        if (createData.time_count > limitTimeCount) {
            createData.time_count = 1
        }
        let result: boolean|string
        result = validate.selectRequired(newAchievementTimeType)
        errorMessage.achievement_time_type_id = result !== true ? result : ''
    }
)
// 達成目安時間
watch(
    () => [createData.achievement_time_type_id, createData.time_count ],
    ([newAchievementTimeType, newTimeCount ]) => {
        let result: boolean|string
        result = validate.stepTimeCount(newTimeCount, newAchievementTimeType)
        errorMessage.time_count = result !== true ? result : ''
    }
)
// サブステップ
watch(
    () => createData.sub_steps,
    () => {
        validSubStepCounts.value = validSubStepCount()
        partialValidSubStepCounts.value = partialValidSubStepCount()
    }, { deep: true }
)
// 初期化
const init = () => {
    // 編集画面か判定
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

// 画像切り抜き
const cropperUrl = ref('')
const setCropperUrl = (e) => {
    cropperUrl.value = ''
    // cropper再レンダリングのために少し待つ
    setTimeout(() => {
        cropperUrl.value = e
    }, 100)
}
const cropperRef = ref()
const presignedUploadRef = ref<InstanceType<typeof PresignedUploadInput>>()

const trimmingUpload = async (): Promise<string> => {
    const fileCanvasData: HTMLCanvasElement = cropperRef.value.getCroppedCanvas()
    return new Promise<string>((resolve) => {
        fileCanvasData.toBlob(async (blob) => {
            let fileName = ''
            if (createData.image_url) {
                const tempFileName = createData.image_url.split('/').pop()
                fileName = tempFileName ?? 'default.png'
            }
            fileName = fileName.substring(0, fileName.lastIndexOf('.')) + '.png'
            // 署名付きURLAPIを使ってファイルアプロ
            const uploadFilePath = await presignedUploadRef.value!.blobUpload(blob!, fileName, 'image/png')
            // 更新時URLとして設定
            createData.image_url = uploadFilePath
            resolve(uploadFilePath)
        })
    })
}
const trimmingImage = async () => {
    const uploadFilePath = await trimmingUpload()
    // 更新時URLとは別に切り抜きコンポーネントの画像URL設定
    await setCropperUrl(uploadFilePath)
}
watch (
    () => createData.image_url,
    async (newData) => await setCropperUrl(newData)
)
const zoom = (per: number) => {
    cropperRef.value.relativeZoom(per)
}
</script>

<template>
    <BaseView className="p-container--steps-form">
        <template v-slot:content>
                <CommonModal
                    title="AI入力補完について"
                    v-show="helpMode"
                    @close="helpMode = false"
                >
                    <template v-slot:content>
                        <div class="c-common-modal__how-to-use-ai__container">
                            <div class="c-how-to-use-ai u-margin-b-1p">
                                <p class="u-margin-b-2p">執筆中の文章を元にAIがサブステップの具体的な内容を考えてくれます。</p>
                                <h3 class="u-font-b u-margin-b-1p">[利用手順]</h3>
                                <div class="c-how-to-use-ai__body">
                                    <div class="u-margin-b-2p">
                                        <p class="u-font-b">①サブステップの名前を入力する</p>
                                        <p>概要をサブステップのタイトル欄に「どんなことをするか」を入力します。</p>
                                    </div>
                                    <div class="u-margin-b-2p">
                                        <p class="u-font-b">②サブステップの詳細を入力する</p>
                                        <p>詳細に具体的な内容を入力します。1文字でも入力すれば、AIがタイトルから具体的な内容を考え、入力欄に反映してくれます。</p>
                                    </div>
                                    <div class="u-margin-b-2p">
                                        <p  class="u-font-b">③AIが提案した内容を確認する</p>
                                        <p>AIが提案してくれた内容を確認し、必要に応じて修正を加えます。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </CommonModal>

                <div class="p-step-edit-form">
                    <!-- start:p-step-edit-form__container -->
                    <div class="p-step-edit-form__editor">
                        <div class="p-step-edit-form__head">
                            <h2 class="c-title--step-edit">{{ pageTitle }}</h2>
                        </div>
                        <div class="p-step-edit-form__body">
                            <!-- ステップ名 -->
                            <div class="p-step-edit-form__element">
                                <h2 class="c-title--step-form">アイキャッチ画像</h2>
                            </div>
                            <div class="p-step-edit-form__element">
                                <PresignedUploadInput
                                    ref="presignedUploadRef"
                                    :previewMode="false"
                                    optional
                                    label="サムネイル(10MBまで)"
                                    v-model:previewUrl="createData.image_url"
                                />
                            </div>
                            <div class="p-step-edit-form__element">
                                <template v-if="isInitialImageUrl">
                                    <span class="c-message--annotation">画像未設定の場合は、デフォルトの画像が設定されます</span>
                                </template>
                            </div>
                            <!-- 画像切り抜き(URLが設定されるのを待ってレンダリング) -->
                            <template v-if="cropperUrl">
                                <div class="p-step-edit-form__element">
                                    <span class="c-message--cropper u-margin-b-1p">画像の切り抜き</span>
                                    <span class="c-message--annotation u-margin-b-1p">アイキャッチ表示にフィットするようサイズ調整されています。<br>画像のメイン部分を中央にして切り抜きボタンをおしてください</span>
                                    <div class="c-image-cropper">
                                        <VueCropper
                                            ref="cropperRef"
                                            :src="cropperUrl"
                                            autoCrop
                                            :aspect-ratio="12 / 6.3"
                                            :min-container-width="300"
                                            :min-container-height="300"
                                            :cropBoxResizable="false"
                                            :img-style="{ 'width': '400px', 'height': '300px' }"
                                            :zoomable="true"
                                            :movable="false"
                                            :zoomOnWheel="false"
                                            :checkCrossOrigin="false"
                                            crossorigin="Anonymous"
                                        ></VueCropper>
                                    </div>
                                    <div class="c-image-cropper__btn-box">
                                        <button class="c-btn--zoom-in u-margin-b-2p" @click="zoom(0.1)">
                                            <span class="material-symbols-outlined">zoom_in</span>
                                            ズームイン
                                        </button>
                                        <button class="c-btn--zoom-out u-margin-b-2p" @click="zoom(-0.1)">
                                            <span class="material-symbols-outlined">zoom_in</span>
                                            ズームアウト
                                        </button>
                                        <button class="c-btn--crop u-margin-b-2p" @click="trimmingImage()">
                                            <span class="material-symbols-outlined">crop</span>
                                            切り抜き
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div class="p-step-edit-form__element u-margin-b-5p">
                                <BorderLine />
                            </div>
                            <div class="p-step-edit-form__element">
                                <h2 class="c-title--step-form">基本情報</h2>
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
                                    :errorMessage="errorMessage.name"
                                />
                            </div>
                            <!-- カテゴリー -->
                            <div class="p-step-edit-form__element">
                                <CategorySelectBox
                                    :class="'u-margin-b-2p'"
                                    ref="categorySelect"
                                    v-model:value="createData.category_id"
                                    label="カテゴリー"
                                    required
                                    :error-message="errorMessage.category_id"
                                />
                            </div>
                            <!-- 達成目安時間 -->
                            <div class="p-step-edit-form__element">
                                <AchievementTimeTypeSelectBox
                                    v-model:value="createData.achievement_time_type_id"
                                    label="達成目安時間(単位)"
                                    :initial-option-text="'分,時間,日,週,月,年'"
                                    selectClass="c-input--large"
                                    required
                                    :error-message="errorMessage.achievement_time_type_id"
                                />
                            </div>
                            <div class="p-step-edit-form__element">
                                <NumberInput
                                    v-model:value="createData.time_count"
                                    className="c-input--large u-margin-b-2p"
                                    :label="'達成目安時間(時間' + getValidTimeCountSpan + ')'"
                                    required
                                    :min="1"
                                    :max="timeType.getLimitTimeCount(createData.achievement_time_type_id)"
                                    :error-message="errorMessage.time_count"
                                />
                            </div>
                            <!-- 概要 -->
                            <div class="p-step-edit-form__element">
                                <TextareaInput
                                    v-model:value="createData.summary"
                                    :errorMessage="''"
                                    height="100"
                                    counter
                                    optional
                                    :max="500"
                                    label="概要"
                                />
                            </div>
                            <div class="p-step-edit-form__element u-margin-b-5p">
                                <BorderLine />
                            </div>
                            <div class="p-step-edit-form__element">
                                <h2 class="c-title--step-form">ステップ詳細</h2>
                            </div>

                            <!-- サブステップ -->
                            <div class="p-step-edit-form__substep-form__list">
                                <draggable :list="createData.sub_steps"
                                    item-key="sort_number"
                                    :animation=300
                                    tag="div"
                                    handle=".p-step-edit-form__substep-form-handle"
                                >
                                    <!-- slotで囲う要素は１つにまとめる -->
                                    <template v-slot:item="{ element: subStep, index}">
                                        <div class="p-step-edit-form__substep-form-item">
                                            <div class="p-step-edit-form__substep-form-border">
                                                <BorderLine />
                                            </div>
                                            <!-- ドラッグアイコン -->
                                            <span
                                                class="p-step-edit-form__substep-form-handle"
                                                v-tooltip="{ content: '順序入れ替え', placement: 'bottom', tooltipClass: 'c-tooltip--gray' }"
                                            >
                                                <span class="c-icon--drag-indicator material-symbols-outlined">drag_indicator</span>
                                            </span>
                                            <h3 class="c-title--edit-sub-step u-margin-b-2p">サブステップ{{ String(index + 1) }}</h3>
                                            <div class="p-step-edit-form__element">
                                                <TextInput
                                                    optional
                                                    :errorMessage="''"
                                                    @key-down:shift-enter="completion(index, subStep.name, subStep.detail)"
                                                    v-model:value="subStep.name"
                                                    :label="'タイトル'"
                                                >
                                                </TextInput>
                                            </div>
                                            <div class="p-step-edit-form__element">
                                                <TextareaInput
                                                    optional
                                                    @key-down:shift-enter="completion(index, subStep.name, subStep.detail)"
                                                    v-model:value="subStep.detail"
                                                    :errorMessage="''"
                                                    height="200"
                                                    counter
                                                    :max="2000"
                                                    :label="'詳細'"
                                                    :formId="'substep-' + index.toString()"
                                                >
                                                </TextareaInput>
                                            </div>
                                            <span class="c-message--annotation">*タイトル,詳細共に空欄のサブステップは保存されません</span>
                                            <div class="p-step-edit-form__element">
                                                <div class="p-step-edit-form__substep-form__completion">
                                                    <button
                                                        :disabled="!subStep.name || !subStep.detail"
                                                        @click="completion(index, subStep.name, subStep.detail)"
                                                        class="c-btn c-btn--large c-btn--completion"
                                                    >
                                                        AIを使って入力補完
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- chat GPT入力補完について -->
                                            <div class="p-step-edit-form__explain-text u-margin-b-2p">
                                                <span class="c-title--explain-completion" @click="helpMode = !helpMode">入力補完機能の使い方</span>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                            <!-- サブステップ追加 -->
                            <div class="p-step-edit-form__bottom">
                                <button
                                    @click="addSubStep"
                                    class="c-btn c-btn--large c-btn--add-sub-step"
                                >
                                    サブステップ追加
                                </button>
                                <template v-if="!isEdit">
                                    <button
                                        @click="create"
                                        :disabled="disableSubmit"
                                        class="c-btn c-btn--large c-btn--create"
                                    >
                                        登録して公開
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        @click="update"
                                        :disabled="disableSubmit"
                                        class="c-btn c-btn--large c-btn--update"
                                    >
                                    {{ updateText }}
                                    </button>
                                </template>
                                <template v-if="showDraft">
                                    <button
                                        @click="saveDraft"
                                        class="c-btn c-btn--large c-btn--save-draft"
                                    >
                                    下書き保存
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
