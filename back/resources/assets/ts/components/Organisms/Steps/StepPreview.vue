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
import { repositoryKey } from '../../../types/common/Injection'
import EditIcon from '../../Atoms/EditIcon.vue'
import DeleteTrashBoxIcon from '../../Atoms/DeleteTrashBoxIcon.vue'


const $repositories = inject<Repositories>(repositoryKey)!

const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()
const userStore = useUserStore()

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, }, // プレビュー表示するステップ情報
    readOnly: { required: false, type: Boolean, default: false, }, // 閲覧モード(twitter,編集ボタン表示)
})
// etmis
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
const settedSummary = computed(() => {
    return props.step.summary !== null && props.step.summary !== ''
})
// TODO; ページレベルでAPIから取得した値をinject,他のコンポーネントもinjectしたものを使う
const achievementTimeTypeName = computed(() => {
    switch (props.step.achievement_time_type_id) {
        case 1:
            return '分間'
        case 2:
            return '時間'
        case 3:
            return '日間'
        case 4:
            return '週間'
        case 5:
            return 'ヶ月間'
        case 6:
            return '年間'
    }
})
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
                <!-- ボタン,twitterシェアボタン -->
                <div v-if="showActionUi" class="c-step-preview__action-ui">
                    <TwitterShareIcon v-if="!requestStore.isLoading" :id="String(step.id!)" :text="step.name" :hashtags="step.category_name!" />
                    <template v-if="isAuthor">
                        <EditIcon className="c-step-preview__edit-icon u-margin-l-2p" :stepId="props.step.id"></EditIcon>
                        <DeleteTrashBoxIcon @click="deleteStep()" className="u-margin-l-2p" :stepId="props.step.id"></DeleteTrashBoxIcon>
                    </template>
                </div>
                <!-- ステップ名 -->
                <h1 class="c-title--step">{{ step.name }}</h1>
                <div v-if="step.image_url" class="c-step-preview__eye-catch">
                    <img class="c-img--step" :src="step.image_url" alt="step-image" />
                </div>
                <!-- 基本情報 -->
                <div class="c-step-preview__information">
                    <CategoryBadge v-if="step.category_id" :id="step.category_id" />
                    <!-- 達成目安時間 -->
                    <template v-if="step.achievement_time_type_id && step.time_count">
                        <p>達成目安時間: {{ String(step.time_count) + achievementTimeTypeName  }}</p>
                    </template>
                    <!-- チャレンジ中のステップ情報表示時 -->
                    <template v-if="isChallengeStep(step)">
                        <div class="c-step-preview__challenge-information">
                            <span>挑戦開始:{{ step.challenged_at }}</span>
                            <template v-if="step.cleared_at">
                                <span>達成:{{ step.cleared_at }}</span>
                            </template>
                        </div>
                    </template>
                    <!-- ステップ詳細画面 -->
                    <template v-if="!isChallengeStep(step) && !!step.created_at">
                        <span>投稿日:{{ step.created_at }}</span>
                    </template>
                </div>
                <div v-if="settedSummary" class="c-step-preview__summary">
                    <h2 class="c-title--step-summary u-margin-b-1p">概要</h2>
                    <p>{{ step.summary }}</p>
                </div>
            </div>
            <!-- ステップ詳細 -->
            <div class="c-step-preview__body">
                <div class="c-step-preview__sub-step__container">
                    <!-- チャレンジ中の表示 (prodビルドエラー解消のためcomputed(subStepList)は使わない)-->
                    <template v-if="isChallengeStep(step)">
                        <template :key="i" v-for="(subStep, i) in step.challenge_sub_steps!">
                            <div class="c-step-preview__sub-step">
                                <!-- サブステップ見出し -->
                                <div class="c-step-preview__sub-step__header">
                                    <h3 class="c-title--sub-step">
                                        <span class="c-step-preview__sub-step__header--prefix">ステップ{{ indexString(i) + ' ' }}</span>
                                        <span class="c-step-preview__sub-step__header--name">{{ subStep.name }}</span>
                                        <template v-if="isChallengeSubStep(subStep)">
                                            <span class="c-step-preview__sub-step__header--status-name" :style="{ 'background-color': statusBgColor(subStep.status) }">
                                                {{ subStep.status_name! }}
                                            </span>
                                        </template>
                                    </h3>
                                </div>
                                <!-- サブステップ詳細 -->
                                <div class="c-step-preview__sub-step__content">{{ subStep.detail }}</div>
                                <!-- クリアボタン -->
                                <div v-if="showClearBtn(subStep)" class="c-step-preview__sub-step__clear-btn">
                                    <button @click="clear(subStep.id!)" class="c-btn--clear">クリア</button>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <template :key="j" v-for="(subStep, j) in step.sub_steps">
                            <div class="c-step-preview__sub-step">
                                <!-- サブステップ見出し -->
                                <div class="c-step-preview__sub-step__header">
                                    <div class="c-title--sub-step">
                                        <span class="c-step-preview__sub-step__header--prefix">ステップ{{ indexString(j) + ' ' }}</span>
                                        <span class="c-step-preview__sub-step__header--name">{{ subStep.name }}</span>
                                    </div>
                                </div>
                                <!-- サブステップ詳細 -->
                                <div class="c-step-preview__sub-step__content">{{ (subStep as SubStep).detail }}</div>
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
.c-edit-tool-tip__txt {
    font-size: 12px;
    cursor: pointer;
    &:hover {
        text-decoration: underline;
    }
}

</style>
