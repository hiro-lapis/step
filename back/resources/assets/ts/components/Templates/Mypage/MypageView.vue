<script setup lang="ts">
import { type Component, onMounted, ref } from 'vue'
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
    { label: '登録済/下書きステップ', value: 'postedStep', color: 'red'},
    { label: 'チャレンジ中ステップ', value: 'challengingStep', color: 'green'},
]

// data
const currentTab = ref(Object.keys(tabs)[0]) // プロフィール編集

onMounted(() => {})
</script>

<template>
    <BaseView className="p-container__mypage">
        <template v-slot:content>
            <div class="p-mypage__container">
                <OutInFadeIn>
                    <MultiTab
                        :tabs="tabGroups"
                        v-model:selectedTab="currentTab"
                    >
                        <template v-slot:content>
                            <KeepAlive>
                                <component :is="tabs[currentTab]"></component>
                            </KeepAlive>
                        </template>
                    </MultiTab>
                </OutInFadeIn>
            </div>
        </template>
    </BaseView>
</template>

<style lang="scss" scoped>
</style>
