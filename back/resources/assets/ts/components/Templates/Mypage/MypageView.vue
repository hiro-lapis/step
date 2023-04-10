<script setup lang="ts">
import { type Component, computed, onMounted, ref } from 'vue'
import MultiTab from '../../Molecules/MultiTab.vue'
import ProfileEdit from '../../Organisms/Mypage/ProfileEdit.vue'
import ChallengingStep from '../../Organisms/Mypage/ChallengingStep.vue'
import PostedStep from '../../Organisms/Mypage/PostedStep.vue'
import OutInFadeIn from '../../Atoms/Transition/OutInFadeIn.vue'

// 動的コンポーネントをinterfaceとして定義し、それを定数の型づけに使う
interface Tabs {
    profileEdit?: Component,
    challengingStep?: Component,
    postedStep?: Component,
}
const tabs: Tabs = {
    profileEdit: ProfileEdit,
    challengingStep: ChallengingStep,
    postedStep: PostedStep,
}

const tabGroups = [
    { label: 'プロフィール編集', value: 'profileEdit', color: '#fff'},
    { label: '登録済ステップ', value: 'postedStep', color: 'red'},
    { label: 'チャレンジ中のステップ', value: 'challengingStep', color: 'green'},
]

// data
// ref内にはtabs の key名を記載
const currentTab = ref(Object.keys(tabs)[0]) // プロフィール編集

onMounted(() => {})
</script>

<template>
    <BaseView className="p-container__mypage">
        <template v-slot:content>
            <div class="c-multi-page__container">
                <OutInFadeIn>
                    <MultiTab
                        :tabs="tabGroups"
                        v-model:selectedTab="currentTab"
                    >
                        <template v-slot:content>
                            <component :is="tabs[currentTab]"></component>
                        </template>
                    </MultiTab>
                </OutInFadeIn>
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
@import "../../../../sass/foundation/_breakpoints.scss";
.c-multi-page {
    &__container {
        width: 100%;
        min-height: 300px;
        transition: all 0.8s ease;
        // @include pc() {
        //     box-shadow: 0 0 2px #ccc;
        // }
    }
    &__head {
        font-size: 24px;
        // 字が詰まって見えるので間隔を調整
        letter-spacing: 2px;
        margin-top: 20px;
        margin-left: 20px;
    }
}
</style>
