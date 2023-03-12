<script setup lang="ts">
import { Step } from '../../types/Step'

// props
defineProps({
    stepList: { require: true, type: Array<Step>, }
})
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <ul class="card-list">
        <template :key="step.id" v-for="step in stepList">
            <li class="card-list__item">
                <router-link :to="{ name: 'steps-show', params: { id: step.id } }" class="card">
                    <div class="card__head">
                        <img src="https://placehold.jp/150x150.png" alt="" class="">
                    </div>
                    <!-- TODO：説明文表示追加 -->
                    <!-- TODO：カテゴリバッジ追加 -->
                    <!-- TODO：設定された画像の表示 -->
                    <h2 class="card__title">{{ step.name }}</h2>
                    <p class="card__txt">
                        テストテキストテストテキストテストテキスト
                        <br>
                        <span>達成目安時間: {{ step.achievement_time_type.name }}</span>
                    </p>
                </router-link>
            </li>
        </template>
    </ul>
</template>

<style scoped lang="scss">

.card-list {
	display: flex;
	flex-direction: column;
	margin-top: -20px; /*1行目の上マージンを相殺*/
    &__item {
        margin-top: 20px;
    }
}

// 768px での2カラム
@media screen and (min-width: 768px),print {
	.card-list {
		flex-direction: row;
		flex-wrap: wrap;
        // 各列の左マージン分合計を除いて1/2サイズで表示
		margin-left: -35px; /*左端列の左マージンを相殺*/
	}
	.card-list__item {
		margin-left: 35px;
        // 各列の左マージン分合計を除いて1/2サイズで表示
		width: calc((100% - 70px) / 2);
	}
}
@media screen and (min-width: 992px),print {
	.card-list__item {
		width: calc((100% - 105px) / 3);
	}
}
@media screen and (min-width: 1200px),print {
	.card-list__item {
		width: calc((100% - 140px) / 4);
	}
}


.card {
	display: block;
	border: 1px solid #e7e7e7;
	border-radius: 5px;
	color: inherit;
	text-decoration: none;
	transition: color .3s;
    &__head {
        position: relative;
        transition: .3s;
        img{
           position: absolute;
           left: 0;
           top: 0;
           max-width: none;
           width: 100%;
           height: 100%;
           object-fit: cover;
           border-radius: 5px 5px 0 0;
       }
    }
    &__head::before {
        content: "";
        display: block;
        padding-top: 56.25%;
    }
    &__title {
        margin-left: 20px;
    }
    &__txt {
        margin: 20px;
    }
    :hover {
        color: #999;
    }
    :hover .card__head {
        opacity: 0.7;
    }
}
/*hover*/
</style>
