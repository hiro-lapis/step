<script setup lang="ts">

// props
defineProps({
    catchCopyText: { required: false, type: String, default: 'あなたのSTEPを共有しよう\n経験シェアサービス', },
    serviceTitle: { required: false, type: String, default: 'STEP', },
    bannerImageUrl: { required: false, type: String, default: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/hero-banner.png', },
})
// data
// methods
// computed
</script>

<template>
    <section class="p-hero-banner__container">
        <div class="p-hero-banner__title">
            <h1 class="c-title--hero-banner">{{ catchCopyText }}</h1>
            <h1 class="c-title--hero-banner">{{ serviceTitle }}</h1>
        </div>
        <div class="p-hero-banner__img">
            <img :src="bannerImageUrl" alt="" class="c-img">
        </div>
    </section>
</template>

<style scoped lang="scss">
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/object/component/_title.scss";
@import "../../../../sass/object/component/_input.scss";

.p-hero-banner {
    position: relative;
    margin-top: 110px;
    margin-bottom: 80px;
    width: 100%;
    height: 400px;
    overflow: hidden;
    &__container {
        max-width: 1000px;
        display: flex;
        margin: 0 auto; // コンテンツ全体は中央寄せ、中身はflex
    }
    &__title {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 35%; // 常にcontainerの幅に対し一定の割合
        margin-left: 5%;
        margin-right: -5%;
        @include mq() {
            margin-left: 0; // 画像との余白調整
            margin-right: 0;
        }
    }
    &__img {
        width: 65%;// 常にcontainerの幅に対し一定の割合
    }

}
</style>
