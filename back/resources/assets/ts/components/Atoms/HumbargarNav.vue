<script setup lang="ts">
import { computed, inject, ref, Ref } from 'vue'
import { useUserStore, useRequestStore } from '../../../ts/store/globalStore'
import { useStepListStore } from '../../../ts/store/stepListStore'
import { useAuthFunc } from '../../composables/auth'
import KeyWordInput from './KeyWordInput.vue'
import { conditionKey } from '../../types/common/Injection'
import { Condition } from '../../types/components/Condition'

// utilities
const userStore = useUserStore()
const requestStore = useRequestStore()
const { fetchData } = useStepListStore()
// data
const condition = inject<Ref<Condition>>(conditionKey)!
const search = inject<() => Promise<void>>('search')!
const spSearch: () => Promise<void> = async () => {
  await search()
  // メニューを閉じる
  isActive.value = false
}
// const search: () => Promise<void> = async () => {
//   // ページ番号を初期化
//   condition.value.page = 1
//   if (requestStore.isLoading) return
//   requestStore.setLoading(true)
//   await fetchData(condition.value)
//   requestStore.setLoading(false)
//   // メニューを閉じる
//   isActive.value = false
// }
const isActive = ref(false)
const isLogin = computed(() => userStore.isLogin)
// methods
const { logout } = useAuthFunc()
</script>

<template>
	<!-- SPメニュー -->
	<nav class="c-sp-nav" :class="{ active: isActive }">
		<ul class="c-sp-nav__list">
      <li class="c-sp-nav__list-item">
        <KeyWordInput
            :placeHolder="''"
            @keyupEnter="spSearch()"
            @clickIcon="spSearch()"
            v-model:value="condition.key_word"
        />
      </li>
			<router-link :to="{ name: 'steps-list' }">
				<li class="c-sp-nav__list-item">
					<a href="#" class="c-sp-nav__list-link">ステップ一覧</a>
				</li>
			</router-link>
			<template v-if="isLogin">
				<router-link :to="{ name: 'steps-create' }">
					<li class="c-sp-nav__list-item">
						<a href="#" class="c-sp-nav__list-link">ステップを登録</a>
					</li>
				</router-link>
				<router-link :to="{ name: 'mypage' }">
					<li class="c-sp-nav__list-item">
						<a href="#" class="c-sp-nav__list-link">マイページ</a>
					</li>
				</router-link>
                <a href="" @click="logout('top')">
                    <li class="c-sp-nav__list-item">
                        <a class="c-sp-nav__list-item">ログアウト</a>
                    </li>
                </a>
			</template>
      <template v-else>
        <router-link :to="{ name: 'login' }">
          <li class="c-sp-nav__list-item">
            <a href="#" class="c-sp-nav__list-link">ログイン</a>
          </li>
        </router-link>
        <router-link :to="{ name: 'register' }">
          <li class="c-sp-nav__list-item">
            <a href="#" class="c-sp-nav__list-link">新規登録</a>
          </li>
        </router-link>
      </template>
		</ul>
	</nav>
	<label for="icon-checkbox" class="c-sp-menu-icon" :class="{ active: isActive }" @click="isActive = !isActive">
            <span class="c-sp-menu-icon__body"></span>
	</label>
</template>

<style lang="scss" scoped>
@import '../../../sass/foundation/breakpoints';
@import '../../../sass/foundation/_variable';

/* 3.SPメニュー */
.c-sp-nav {
    position: absolute;
    top: 50px;
    left: 0;
    width: 100%;
    height: calc(100vh + 50px);
    background: $color_base;
    transform: translateX(100%);
    transition: 0.5s;
    &.active {
      visibility: visible;
      opacity: 1;
      transition-delay: 0s;
      transform: translateX(0%);
    }
      @include mq() {
        display: none;
      }
    &__list {
      height: 100%;
      display: flex;
      flex-direction: column;
      color: $color_base;
      border-bottom: 1px solid $color_base;
      &-item {
        padding-bottom: 15px;
        padding-top: 15px;
        border-bottom: $color_base 1px solid;
      }
    }
}


// ④SPアイコン
// ハンバーガーメニューアイコン
.c-sp-menu-icon {
  display: block;
  position: relative;
  width: 25px;
  height: 100%;
  cursor: pointer;
  transition: transform 0.3s ease-in;
  opacity: 1;
  @include mq() {
    opacity: 0;
    display: none;
  }
  &__body {
    display: block;
    position: absolute;
    top: 4px;
    // top: 55%;
    margin-top: -0.3em;
    width: 100%;
    height: 0.2em;
    text-align: initial;
    border-radius: 1px;
    background-color: $color_base;
    transition: transform 0.3s ease-in;
    &:after,
    &:before {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 1px;
      background-color: #eeeeee;
      transition: transform 0.3s ease;
    }
    &:before {
      transform: translateY(-0.6em);
    }
    &:after {
      transform: translateY(0.6em);
    }
  }
}

.active {
  // checkした時の表示
  & .c-sp-menu-icon__body {
    transform: rotate(45deg);
  }
  & .c-sp-menu-icon__body:after,
  & .c-sp-menu-icon__body:before {
  transform: rotate(90deg);
  }
  // & .c-sp-nav {

  // }
}
</style>
