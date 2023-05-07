<script setup lang="ts">
import { computed, inject, ref, Ref } from 'vue'
import { useUserStore } from '../../../ts/store/globalStore'
import { useAuthFunc } from '../../composables/auth'
import KeyWordInput from './KeyWordInput.vue'
import { conditionKey, searchFuncKey } from '../../types/common/Injection'
import { Condition } from '../../types/components/Condition'

// utilities
const userStore = useUserStore()
// data
const condition = inject<Ref<Condition>>(conditionKey)!
const search = inject<() => Promise<void>>(searchFuncKey)!
const spSearch: () => Promise<void> = async () => {
  await search()
  // SPメニューを閉じる
  isActive.value = false
}
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
