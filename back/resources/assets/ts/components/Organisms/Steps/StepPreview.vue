<script setup lang="ts">
import { computed, inject } from 'vue'
import { ChallengeStep } from 'assets/ts/types/ChallengeStep';
import { PropType } from 'vue'
import { Step } from '../../../types/Step'
import CategoryBadge from '../../Atoms/CategoryBadge.vue'
import { useTypeGuards } from '../../../composables/typeGuards'
import { Repositories } from '../../../apis/repositoryFactory'
import { ChallengeStatusJudgement } from '../../../composables/ChallengeStatus'
import { useMessageInfoStore, useRequestStore } from '../../../store/globalStore'

const $repositories = inject<Repositories>("$repositories")!

const messageStore = useMessageInfoStore()
const requestStore = useRequestStore()

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, }, // プレビュー表示するステップ情報
    readOnly: { required: false, type: Boolean, default: false, }, // 見るだけモードか
})

// computed
const { isChallengeStep, isChallengeSubStep } = useTypeGuards()
// ステップ・チャレンジステップの型が異なるため、型に応じたサブステップを表示
const subSteps = computed(() => {
    if ('sub_steps' in props.step ) {
        return props.step.sub_steps
    } else {
        return props.step.challenge_sub_steps
    }
})

// methods
const { isInChallenge, isCleard } = ChallengeStatusJudgement()
const statusBgColor = (status: number): string => {
    if (isInChallenge(status)) return '#ea352e'
    if (isCleard(status)) return '#65c99b'
    return '#666'
}
// ログインユーザーがサブステップをクリア
const clear = async (subStepId: number) => {
    if (requestStore.isLoading) return
    requestStore.setLoading(true)
    await $repositories.challengeStep.clear(subStepId)
        .then(response => {
            console.log(response)
            messageStore.setMessage(response.data.message)
        }).finally(() => {
            requestStore.setLoading(false)
        })
}
</script>

<template>
    <div class="c-step-preview">
        <div class="c-step-preview__container">
            <div class="c-step-preview__head">
                <!-- ステップ名 -->
                <h1 class="c-title--step">{{ step.name }}</h1>
                <div class="c-step-preview__information">
                    <CategoryBadge v-if="step.category_id" :id="step.category_id" />
                    <template v-if="!!step.created_at">
                        <template v-if="isChallengeStep(step)">
                            <div class="c-step-preview__challenge-information">
                                <span class="u-margin-l-1p">挑戦開始:{{ step.challenged_at }}</span>
                                <template v-if="step.cleared_at">
                                    <span class="u-margin-l-1p">達成:{{ step.cleared_at }}</span>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <span class="u-margin-l-1p">{{ step.created_at }}</span>
                        </template>
                    </template>
                </div>
                <!-- TODO: メリットや達成時間目安などの情報 -->
            </div>
            <!-- ステップ詳細 -->
            <div class="c-step-preview__body">
                <div class="c-sub-step__container">
                    <template v-for="(subStep, index) in subSteps">
                        <div class="c-sub-step">
                            <!-- サブステップ見出し -->
                            <div class="c-sub-step__header">
                                <div class="c-title--sub-step">
                                    <span class="c-sub-step__header--prefix">ステップ{{ (index + 1).toString() + ' ' }}</span>
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
                            <slot name="subStepBottom"></slot>
                        </div>
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
    padding: 0px 20px 20px;
    width: 100%; // 親要素の幅いっぱいに広げる
    overflow-wrap: break-word;
    word-wrap: break-word; // 溢れる文字を折り返す
    &__head {
        margin-bottom: 30px;
    }
    &__information {
        display: flex;
        align-items: center;
    }
    &__challenge-information {
        font-size: 12px;
    }
    &__container {
        overflow: hidden; // 見出し線など溢れるデザインを非表示
    }
    &__bottom {
        display: flex;
        justify-content: center;
    }
}
.c-sub-step {
    margin-bottom: 25px;
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
    &__clear-btn__container {
        position: absolute;
        right: 0%;
        bottom: 1%;
    }
}

</style>
