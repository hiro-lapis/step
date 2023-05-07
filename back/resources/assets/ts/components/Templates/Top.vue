<script setup lang="ts">
import HeroBanner from '../Organisms/Top/HeroBanner.vue'
import CategoryIntroduction from '../Organisms/Top/CategoryIntroduction.vue'
import FeatureIntrocution from '../Organisms/Top/FeatureIntrocution.vue'
import MainPageLink from '../Organisms/Top/MainPageLink.vue'

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
</style>
