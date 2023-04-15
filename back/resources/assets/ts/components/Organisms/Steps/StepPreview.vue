<script setup lang="ts">
import { computed } from 'vue'
import { ChallengeStep } from 'assets/ts/types/ChallengeStep';
import { PropType } from 'vue'
import { Step } from '../../../types/Step'
import CategoryBadge from '../../Atoms/CategoryBadge.vue'

// props
const props = defineProps({
    step: { required: true, type: Object as PropType<Step|ChallengeStep>, }, // プレビュー表示するステップ情報
    readOnly: { required: false, type: Boolean, default: false, }, // 見るだけモードか
})

// computed
// ステップ・チャレンジステップの型が異なるため、型に応じたサブステップを表示
const subSteps = computed(() => {
    if ('sub_steps' in props.step ) {
        return props.step.sub_steps
    } else {
        return props.step.challenge_sub_steps
    }
})
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
                        <span class="u-margin-l-1p">{{ step.created_at }}</span>
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
                                <h2 class="c-title--sub-step">
                                    <span class="c-sub-step__header--prefix">ステップ{{ (index + 1).toString() + ' ' }}</span>
                                    <span class="c-sub-step__header--name">{{ subStep.name }}</span>
                                </h2>
                                <!-- TODO画像設定の有無に応じて画像表示 -->
                                <!-- <template v-if="subStep"></template> -->
                            </div>
                            <!-- サブステップ詳細 -->
                            <div class="c-sub-step__content">{{ subStep.detail }}</div>
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
    }
    &__content {
        font-size: 13px;
    }
}

</style>
