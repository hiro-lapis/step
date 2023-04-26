<script setup lang="ts">
</script>

<template>
    <div class="c-main-page-link__container">
        <div class="c-main-page-link">
            <div class="c-main-page-link__title">
                <h2 class="c-title--main-page-link">ステップを見る</h2>
            </div>
            <div class="c-main-page-link__text">
                <router-link :to="{ name: 'steps-list' }" class="c-btn--step-list">
                    ステップ一覧
                </router-link>
            </div>
        </div>
        <div class="c-main-page-link">
            <div class="c-main-page-link__text">
                <h2 class="c-title--main-page-link">ステップを投稿</h2>
            </div>
            <div class="c-main-page-link__text">
                <router-link :to="{ name: 'register' }" class="c-btn--register">
                    新規登録
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/foundation/_variable.scss";
@import "../../../../sass/object/component/_title.scss";
@import "../../../../sass/object/component/_input.scss";

.c-main-page-link {
    width: 90%;
    margin-bottom: 30px;
    @include mq() {
        width: 40%;
        margin-bottom: 0px;
    }
    &__container {
        display: flex;
        flex-direction: column;
        align-items: center;
        @include mq() {
            flex-direction: row;
            justify-content: space-around;
            width: 100%;
        }
    }
    &__title {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
        @include pc() {
            margin-bottom: 0px;
        }
    }
    &__text {
        display: flex;
        justify-content: center;
    }
}
</style>
