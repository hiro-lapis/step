<script setup lang="ts">
import { inject, onMounted } from 'vue'
import LoadingIcon from '../Atoms/LoadingIcon.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'
import { guestPageName } from '../../routes/routes'
import HeroBanner from '../Organisms/Top/HeroBanner.vue'
import CategoryIntroduction from '../Organisms/Top/CategoryIntroduction.vue'
import FeatureIntrocution from '../Organisms/Top/FeatureIntrocution.vue'
import MainPageLink from '../Organisms/Top/MainPageLink.vue'

// utilities
const router = useRouter()
const userStore = useUserStore()
const $repositories = inject<Repositories>("$repositories")!
console.log(userStore)
// props
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <BaseView className="p-container--top">
        <template v-slot:content>
            <!-- 1.hero banner -->
            <section class="p-top__hero-banner">
                <HeroBanner />
            </section>
            <!-- 2.feature -->
            <section class="p-top__feature-list">
                <FeatureIntrocution />
            </section>
            <!-- 3.category -->
            <section class="p-top__category-list">
                <h2 class="c-title u-margin-b-2p">興味が湧くステップがきっと見つかる</h2>
                <CategoryIntroduction />
            </section>
            <!-- 4.register-form -->
            <section class="p-top__category-list">
                <h2 class="c-title u-margin-b-2p">さっそくSTEPをみよう！</h2>
                <MainPageLink />
            </section>
        </template>
    </BaseView>
</template>

<style scoped lang="scss">
@import "../../../sass/foundation/_breakpoints.scss";
@import "../../../sass/object/component/_title.scss";
@import "../../../sass/object/component/_input.scss";

.p-top {
    &__hero-banner {
        background-color: #faebd7;
    }
    &__feature-list {
        background-color: #fff;
    }
    &__feature-list,
    &__category-list {
        padding-top: 50px;
        padding-bottom: 50px;
        // background-color: #fff;
        @include pc() {
            padding-top: 100px;
            padding-bottom: 100px;
        }
    }
}
</style>
