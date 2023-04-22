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
    <BaseView>
        <template v-slot:content>
            <!-- 1.hero banner -->
            <section class="p-hero-banner__container">
                <div class="p-hero-banner__img">
                    <img :src="'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/hero-banner.png'" alt="" class="c-img">
                </div>
                <div class="p-hero-banner__title">
                    <h1 class="c-title">あなたの人生のSTEPを共有しよう</h1>
                </div>

            </section>
        </template>
    </BaseView>
</template>

<style scoped lang="scss">
@import "../../../sass/foundation/_breakpoints.scss";
@import "../../../sass/object/component/_title.scss";
@import "../../../sass/object/component/_input.scss";

.p-hero-banner {
    position: relative;
    margin-top: 110px;
    margin-bottom: 80px;
    width: 100%;
    height: 400px;
    overflow: hidden;
    &__title {
        position: absolute;
        top: 240px;
        right: 160px;
        width: 100%;
        text-align: center;
        font-size: 3rem;
        letter-spacing: 0.1em;
        line-height: 1.2;
        @include pc() {
            width: 550px;
        }
    }
}
</style>
