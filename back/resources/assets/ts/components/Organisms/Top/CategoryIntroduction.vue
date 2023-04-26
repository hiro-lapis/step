<script setup lang="ts">
import { CategoryColorList } from '../../../types/components/CategoryColor'

// props

// https://placehold.jp/150x150.png

// data
const catetoryList = [
    { name: 'プログラミング', color: CategoryColorList[0].rgba,image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/1.png' },
    { name: '英語', color: CategoryColorList[1].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/2.png' },
    { name: 'ダイエット', color: CategoryColorList[2].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/3.png' },
    { name: '朝活', color: CategoryColorList[3].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/4.png' },
]
// methods
// computed

</script>

<template>
    <ul class="c-category-card__container">
        <template :key="category.name" v-for="category in catetoryList">
            <li class="c-category-card">
                <div class="c-category-card__head" :style="{ backgroundColor: category.color }">
                    <img :src="category.image_url" alt="" class="">
                </div>
                <p class="c-category-card__body">
                    {{ category.name }}
                </p>
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">
@import "../../../../sass/foundation/_breakpoints.scss";
@import "../../../../sass/object/component/_title.scss";
@import "../../../../sass/object/component/_input.scss";

.c-category-card { // カード本体
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 50px;
    width: 100px;
    @include mq() {
        width: 150px;
    }
    @include pc() {
        margin-bottom: 0px;
    }
    &__container { // listのコンテナ
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        @include mq() {
            flex-direction: row;
        }
        @include pc() { // PC では横並び,高さもコンテンツに合わせる
            flex-wrap: wrap;
            justify-content: space-around;
        }
    }
    &__head { // カテゴリーアイコン(丸クリップ)
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 20%;
        border-radius: 50%;
        @include pc() {
            padding: 25px;
        }

    }
    &__title {
        margin-bottom: 10px;
        text-align: center;
    }
    &__body {
        font-size: 12px;
        letter-spacing: 1.5px;
        line-height: 1.3;
    }
}
</style>
