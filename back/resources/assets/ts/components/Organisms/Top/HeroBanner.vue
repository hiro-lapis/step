<script setup lang="ts">
// props
defineProps({
    catchCopyText: { required: false, type: String, default: 'あなたのSTEPを共有しよう\n経験シェアサービス', },
    serviceTitle: { required: false, type: String, default: 'STEP', },
    bannerImageUrl: { required: false, type: String, default: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/hero-banner.png', },
})
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
</style>
