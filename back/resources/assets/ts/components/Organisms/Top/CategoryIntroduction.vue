<script setup lang="ts">
import { CategoryColorList } from '../../../types/components/CategoryColor'

// data
const catetoryList = [
    { name: 'プログラミング', color: CategoryColorList[0].rgba,image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/1.png' },
    { name: '英語', color: CategoryColorList[1].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/2.png' },
    { name: 'ダイエット', color: CategoryColorList[2].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/3.png' },
    { name: '朝活', color: CategoryColorList[3].rgba, image_url: 'https://graduation-step.s3.ap-northeast-1.amazonaws.com/public/common/category-icon/4.png' },
]
</script>

<template>
    <ul class="p-category-card__container">
        <template :key="category.name" v-for="category in catetoryList">
            <li class="p-category-card">
                <div class="p-category-card__head" :style="{ backgroundColor: category.color }">
                    <img :src="category.image_url" alt="" class="">
                </div>
                <p class="p-category-card__body">
                    {{ category.name }}
                </p>
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">
</style>
