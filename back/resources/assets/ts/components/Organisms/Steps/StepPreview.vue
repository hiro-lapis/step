<script setup lang="ts">
import { computed, inject, PropType } from 'vue'
import { useMessageInfoStore, useUserStore, useRequestStore } from '../../../store/globalStore'
import { useTypeGuards } from '../../../composables/typeGuards'
import { ChallengeStatusJudgement } from '../../../composables/ChallengeStatus'
import { Repositories } from '../../../apis/repositoryFactory'
import { ChallengeStep } from 'assets/ts/types/ChallengeStep'
import { ChallengeSubStep } from 'assets/ts/types/ChallengeSubStep'
import { Step } from '../../../types/Step'
import { SubStep } from 'assets/ts/types/SubStep'
import CategoryBadge from '../../Atoms/CategoryBadge.vue'
import EditToolTip from '../../Atoms/EditToolTip.vue'
import TwitterShareIcon from '../../Atoms/TwitterShareIcon.vue'
import { RouterLocation } from '../../../types/common/Router'

const $repositories = inject<Repositories>('$repositories')!

const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const userStore = useUserStore()

// props
// const props = defineProps<StepProps>()

const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, }, // プレビュー表示するステップ情報
    readOnly: { required: false, type: Boolean, default: false, }, // 閲覧モード(twitter,編集ボタン表示)
})

interface Emits {
    (e: 'clear'): void;
    (e: 'delete'): void;
}
const emit = defineEmits<Emits>()
const editToolTipMenus = [
    {
        name: '編集',
        icon: 'edit',
        to: { name: 'steps-edit', params: { id: props.step.id }} as RouterLocation,
    },
]
// computed
const { isChallengeStep, isChallengeSubStep } = useTypeGuards()
// ステップ・チャレンジステップの型が異なるため、型に応じたサブステップを表示
// const subStepList = computed(() => {
//     if ('sub_steps' in props.step ) {
//         return props.step.sub_steps!
//     } else {
//         return props.step.challenge_sub_steps!
//     }
// })

// methods
const { isInChallenge, isCleard } = ChallengeStatusJudgement()
// サブステップのインデックスを文字列で返す
const indexString = (index: number): string => {
    return (index + 1).toString()
}
// サブステップのステータスに応じた背景色を返す
const statusBgColor = (status: number): string => {
    if (isInChallenge(status)) return '#ea352e'
    if (isCleard(status)) return '#65c99b'
    return '#666'
}
const showClearBtn = (subStep: SubStep|ChallengeSubStep): boolean => {
    return isChallengeSubStep(subStep) && isInChallenge(subStep.status)
}
const isAuthor = computed(() => {
    return userStore.isLogin && (props.step as Step).user_id! === userStore.user.id
})
// Twitterシェア,編集ボタンを表示領域を表示するか
const showActionUi = computed(() => {
    return props.readOnly && !requestStore.isLoading
})
const deleteStep = async () => {
    if (!confirm('削除しますか？')) return
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.step.delete(props.step.id!)
        .then(response => {
            messageStore.setMessage(response.data.message)
            // 親へ削除イベントを発火。削除後の挙動はページ側で制御
            emit('delete')
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
// ログインユーザーがサブステップをクリア
const clear = async (subStepId: number) => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.challengeStep.clear({ challenge_step_id: (props.step as ChallengeStep).id, challenge_sub_step_id: subStepId})
        .then(response => {
            messageStore.setMessage(response.data.message)
            // 親へクリアイベントを発火してチャレンジ情報更新
            emit('clear')
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
</script>

<template>
    <div class="c-step-preview">
        <div class="c-step-preview__container">
            <div class="c-step-preview__head">
                <div v-if="showActionUi" class="c-step-preview__action-ui">
                    <span class="c-step-card__edit-icon">
                        <TwitterShareIcon v-if="!requestStore.isLoading" :id="'step-preview'" :text="step.name" :hashtags="step.category_name!" />
                        <span v-if="isAuthor" class="u-margin-l-2p">
                            <EditToolTip
                                :menus="editToolTipMenus"
                            >
                            <template v-slot:bottom>
                                <p class="c-edit-tool-tip__txt" @click="deleteStep">削除</p>
                                <!-- <ConfirmDialog :open-txt="'削除'">
                                </ConfirmDialog> -->
                            </template>
                            </EditToolTip>
                        </span>
                    </span>
                </div>
                <!-- ステップ名 -->
                <h1 class="c-title--step">{{ step.name }}</h1>
                <div class="c-step-preview__information">
                    <CategoryBadge v-if="step.category_id" :id="step.category_id" />
                    <!-- チャレンジ中のステップ情報表示時 -->
                    <template v-if="isChallengeStep(step)">
                        <div class="c-step-preview__challenge-information">
                            <span class="u-margin-l-1p">挑戦開始:{{ step.challenged_at }}</span>
                            <template v-if="step.cleared_at">
                                <span class="u-margin-l-1p">達成:{{ step.cleared_at }}</span>
                            </template>
                        </div>
                    </template>
                    <!-- ステップ詳細画面 -->
                    <template v-if="!isChallengeStep(step) && !!step.created_at">
                        <span class="u-margin-l-1p">投稿日:{{ step.created_at }}</span>
                    </template>
                </div>
            </div>
            <!-- ステップ詳細 -->
            <div class="c-step-preview__body">
                <div class="c-sub-step__container">
                    <!-- チャレンジ中の表示 (prodビルドエラー解消のためcomputed(subStepList)は使わない)-->
                    <template v-if="isChallengeStep(step)">
                        <template :key="i" v-for="(subStep, i) in step.challenge_sub_steps!">
                            <div class="c-sub-step">
                                <!-- サブステップ見出し -->
                                <div class="c-sub-step__header">
                                    <div class="c-title--sub-step">
                                        <span class="c-sub-step__header--prefix">ステップ{{ indexString(i) + ' ' }}</span>
                                        <span class="c-sub-step__header--name">{{ subStep.name }}</span>
                                        <template v-if="isChallengeSubStep(subStep)">
                                            <span class="c-sub-step__header--status-name" :style="{ 'background-color': statusBgColor(subStep.status) }">
                                                {{ subStep.status_name! }}
                                            </span>
                                        </template>
                                    </div>
                                </div>
                                <!-- サブステップ詳細 -->
                                <div class="c-sub-step__content">{{ subStep.detail }}</div>
                                <!-- クリアボタン -->
                                <div v-if="showClearBtn(subStep)" class="c-sub-step__clear-btn">
                                    <button @click="clear(subStep.id!)" class="c-btn--clear">クリア</button>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <template :key="j" v-for="(subStep, j) in step.sub_steps">
                            <div class="c-sub-step">
                                <!-- サブステップ見出し -->
                                <div class="c-sub-step__header">
                                    <div class="c-title--sub-step">
                                        <span class="c-sub-step__header--prefix">ステップ{{ indexString(j) + ' ' }}</span>
                                        <span class="c-sub-step__header--name">{{ subStep.name }}</span>
                                    </div>
                                </div>
                                <!-- サブステップ詳細 -->
                                <div class="c-sub-step__content">{{ (subStep as SubStep).detail }}</div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
            <!-- 挑戦ボタンなど -->
            <div class="c-step-preview__bottom">
                <slot name="bottom"></slot>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

.c-step-preview {
    background-color: #fff;
    padding: 20px;
    width: 100%; // 親要素の幅いっぱいに広げる
    overflow-wrap: break-word;
    word-wrap: break-word; // 溢れる文字を折り返す
    box-sizing: border-box;
    &__head {
        margin-bottom: 30px;
    }
    &__action-ui {
        margin-bottom: 5px;
        display: flex;
        justify-content: right;
    }
    &__information {
        display: flex;
        align-items: center;
    }
    &__challenge-information {
        font-size: 12px;
    }
    &__bottom {
        display: flex;
        justify-content: center;
    }
}
.c-edit-tool-tip__txt {
    font-size: 12px;
    cursor: pointer;
    &:hover {
        text-decoration: underline;
    }
}

.c-sub-step {
    margin-bottom: 25px;
    overflow: hidden; // 見出し線など溢れるデザインを非表示
    &__header {
        margin-bottom: 20px;
        &--status-name {
            display: inline-block;
            margin-left: 5px;
            padding: 2px 5px;
            font-size: 10px;
            vertical-align: text-bottom;
            color: #fff;
            border-radius: 5px;
        }
    }
    &__content {
        font-size: 13px;
    }
    &__clear-btn {
        margin-top: 20px;
    }
}

</style>
