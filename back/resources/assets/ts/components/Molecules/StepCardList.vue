<script setup lang="ts">
import { Step } from '../../types/Step'
import StepCard from '../Atoms/StepCard.vue'

// props
defineProps({
    stepList: { require: true, type: Array<Step>, }
})
</script>

<template>
    <ul class="c-step-card-list">
        <template :key="i" v-for="(step, i) in stepList">
            <li class="c-step-card-list__item">
                <StepCard :step="step" />
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">

.c-step-card-list {
	display: flex;
	flex-direction: column;
	margin-top: -20px; /*1行目の上マージンを相殺*/
    &__item {
        margin-top: 20px;
    }
}

// 768px での2カラム
@media screen and (min-width: 768px),print {
	.c-step-card-list {
		flex-direction: row;
		flex-wrap: wrap;
        // 各列の左マージン分合計を除いて1/2サイズで表示
		margin-left: -35px; /*左端列の左マージンを相殺*/
	}
	.c-step-card-list__item {
		margin-left: 35px;
        // 各列の左マージン分合計を除いて1/2サイズで表示
		width: calc((100% - 70px) / 2);
	}
}
// 3列
// @media screen and (min-width: 992px),print {
// 	.c-step-card-list__item {
// 		width: calc((100% - 105px) / 3);
// 	}
// }
// 4列
// @media screen and (min-width: 1200px),print {
// 	.c-step-card-list__item {
// 		width: calc((100% - 140px) / 4);
// 	}
// }
</style>
