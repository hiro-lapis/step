<script setup lang="ts">
import { computed, inject, ref } from 'vue'
import { useUserStore } from '../../../ts/store/globalStore'
import { Repositories } from '../../apis/repositoryFactory'

const userStore = useUserStore()
const $repositories = inject<Repositories>('$repositories')!

const isActive = ref(false)

const isLogin = computed(() => userStore.isLogin)
const logout = () => {
  userStore.setLogin(false)
}
</script>

<template>
	<!-- SPメニュー -->
	<nav class="c-sp-nav" :class="{ active: isActive }">
		<ul class="c-sp-nav__list">
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
					<li @click="logout" class="c-nav__list-item">
						<span class="c-nav__list-link">ログアウト</span>
					</li>
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
    background-color: #eeeeee;
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
