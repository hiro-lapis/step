<script setup lang="ts">
</script>

<template>
    <div class="p-main-page-link__container">
        <div class="p-main-page-link">
            <div class="p-main-page-link__title">
                <h2 class="c-title--main-page-link">ステップを見る</h2>
            </div>
            <div class="p-main-page-link__text">
                <router-link :to="{ name: 'steps-list' }" class="c-btn--step-list">
                    ステップ一覧
                </router-link>
            </div>
        </div>
        <div class="p-main-page-link">
            <div class="p-main-page-link__text">
                <h2 class="c-title--main-page-link">ステップを投稿</h2>
            </div>
            <div class="p-main-page-link__text">
                <router-link :to="{ name: 'register' }" class="c-btn--register">
                    新規登録
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
</style>
