<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import RequirementText from './RequirementText.vue'
import { repositoryKey } from '../../types/common/Injection'
import ErrorMessage from './ErrorMessage.vue'

// utilities
const $repositories = inject<Repositories>(repositoryKey)!

// props
const props = defineProps({
    errorKey: { required: false, type: String, default: 'category-id' },
    formId: { required: false, type: String, default: 'category-id' },
    label: { required: false, type: String, default: '' },
    errorMessage: { required: false, type: String, default: '' },
    required: { required: false, type: Boolean, default: false },
    selectClass: { required: false, type: String, default: 'u-padding-l-05p' },
    value: { required: true, type: Number, },
})

// data
type Category = {
    id: number
    name: string
}
const categoryList = ref<Category[]>([])
const errorMessage = ref('')
const initialState = ref(true)

// methods
const fetchData = async () => {
    await $repositories.common.category()
        .then(response => {
            categoryList.value = response.data
        })
        .catch(() => {
            console.log('error is occured on fetch categories')
        })
}
interface Emits {
    (e: 'update:value', value: Number): void;
}
const emit = defineEmits<Emits>()
// 親コンポーネントへ更新値の発信
const changedValue = (event: Event) => {
    // セレクトタグのeventとして解釈して値を取得
    const selected = (event.target as HTMLSelectElement).value
    emit('update:value', Number(selected))
}

/**
 * 未選択オプションのテキスト
 * 選択必須かどうかで文章をかえる
 */
const initialOptionText = computed(() => props.required ? '選択してください' : 'カテゴリー')
const notSelected = computed(() => {
    return props.value === 0
})
// 選択必須設定を条件にバリデーションを行う
const validate = (): boolean => {
    if (!props.required) return true

    if (props.value === 0) {
    // if (props.value === 0 || categoryList.value.some(cat => cat.id === props.value)) {
        errorMessage.value = '選択してください'
        return false
    }
    errorMessage.value = ''
    return true
}

// 親コンポーネントから呼び出せるようメソッドを公開
defineExpose({
    validate,
})
onMounted(() => {
    fetchData()
    // 初期値が設定されていたら[選択してください]の選択解除
    initialState.value = props.value !== 0
})
</script>

<template>
    <div class="c-select--category-search__container">
        <label class="c-select--category-search__label" :for="formId">
            {{ label }}
            <template v-if="required">
                <RequirementText />
            </template>
        </label>
            <select
                :value="value"
                @change="changedValue"
                id="cateory_id"
                name="category_id"
                class="c-select--category-search"
                :class="[{ 'u-color--b9': notSelected }, selectClass]"
            >
                <option value="0"
                    :disabled="required">{{ initialOptionText }}</option>
                <option
                    v-for="(category, index) in categoryList"
                    :key="index"
                    :value="category.id">{{category.name}}</option>
            </select>
            <!-- エラーメッセージのキー設定時のみ表示 -->
            <template v-if="!!errorMessage">
                <ErrorMessage class="u-margin-l-1p" :message="errorMessage" />
            </template>
    </div>
</template>

<style lang="scss" scoped>
// エラーメッセージエフェクト
.v-enter,
.v-leave-to {
    opacity: 0;
}
.v-enter-to,
.v-leave {
    opacity: 1;
}
.v-enter-active {
    transition: opacity 100ms;
}
.v-leave-active {
    transition: opacity 100ms;
}
</style>
