<script setup lang="ts">

// props

// https://placehold.jp/150x150.png

// data
// methods
// computed

</script>

<template>
    <ul class="c-feature-card__container">
        <li class="c-feature-card__list">
            <div class="c-feature-card">
                <div class="c-feature-card__head">
                    <img src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/feature1.png" alt="" class="">
                </div>
                <h2 class="c-feature-card__title u-spread">自分に合う学習方法が見つかる</h2>
                <p class="c-feature-card__body">
                    同じ興味を持つ人たちが自分なりに取り組んだ方法・経験を知ることで、自分に合った学習方法を発見できます。
                </p>
            </div>
        </li>
        <li class="c-feature-card__list">
            <div class="c-feature-card">
                <div class="c-feature-card__head">
                    <img src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/feature2.png" alt="" class="">
                </div>
                <h2 class="c-feature-card__title u-spread">着実にレベルアップ</h2>
                <p class="c-feature-card__body">
                    STEPはやること1つづつのSUB STEPに分かれています。段階をふんで達成していくことで、目標を着実に達成することができます。
                </p>
            </div>
        </li>
        <li class="c-feature-card__list">
            <div class="c-feature-card">
                <div class="c-feature-card__head">
                    <img src="https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/feature3.png" alt="" class="">
                </div>
                <h2 class="c-feature-card__title u-spread">仲間を作って楽しく挑戦</h2>
                <p class="c-feature-card__body">
                    同じSTEPに挑戦している人同士でチャットをすることで仲間を見つけましょう。モチベーションの向上やよりあなたに合った取り組み方、学習者同士のコラボレーションなどに役立ちます。
                </p>
            </div>
        </li>
    </ul>
</template>

<style scoped lang="scss">
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/object/component/_title.scss";
@import "../../../../sass/object/component/_input.scss";

.c-feature-card { // カード本体
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 300px; // スマホでも表示できるサイズ
    margin-bottom: 50px;
    @include pc() {
        margin-bottom: 0px;
    }
    &__container { // listのコンテナ
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        @include pc() { // PC では横並び,高さもコンテンツに合わせる
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-around;
        }
    }
    &__list {
        // width: calc((100% - 70px) / 1);
        margin: 0 auto;
        // @include pc() { // PC では横並び
        //     width: calc((100% - 70px) / 2);
        // }
        // @include mq() { // PC では横並び
        //     width: calc((100% - 70px) / 3);
        // }

    }
    &__head {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    &__title {
        margin-bottom: 10px;
        text-align: center;
    }
    &__body {
        font-size: 13px;
        letter-spacing: 1.5px;
        line-height: 1.3;
    }
}
</style>
