<script setup lang="ts">
import { type Component, computed, onMounted, ref } from 'vue'
import ProfileEdit from '../../Organisms/Mypage/ProfileEdit.vue'
import OutInFadeIn from '../../Atoms/Transition/OutInFadeIn.vue'

// 動的コンポーネントをinterfaceとして定義し、それを定数の型づけに使う
interface Tabs {
    profileEdit?: Component,
}
const tabs: Tabs = {
    profileEdit: ProfileEdit
}

// data
// ref内にはtabs の key名を記載
const currentTab = ref('profileEdit')

// computed
const pageTitle = computed(() => {
    switch (currentTab.value) {
        case 'profileEdit':
            return 'プロフィール編集';
    }
})

onMounted(() => {})
</script>

<template>
    <BaseView className="p-container__mypage">
        <template v-slot:content>
            <div class="c-multi-page__container">
                {{ pageTitle }}
                <OutInFadeIn>
                    <keep-alive>
                        <component :is="tabs[currentTab]"></component>
                    </keep-alive>
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
        @include pc() {
            box-shadow: 0 0 2px #ccc;
        }
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
