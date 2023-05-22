<script setup lang="ts">
import { computed, onMounted, PropType } from 'vue'
import RequirementText from './RequirementText.vue'
import ErrorMessage from './ErrorMessage.vue'

// props
const props = defineProps({
    label: { required: false, type: String, default: '' },
    items: { required: false, type: Array<Item>, default: () => [] },
    initialOptionText: { required: false, type: String, default: '選択してください' },
    errorMessage: { required: false, type: String, default: '' },
    showError: { required: false, type: Boolean, default: false},
    required: { required: false, type: Boolean, default: false },
    selectClass: { required: false, type: String, default: 'c-select--item-select' },
    value: { required: true, type: String, },
})

// data
type Item = {
    value: string
    text: string
}
// computed
// methods
interface Emits {
    (e: 'update:value', value: String): void;
}
const emit = defineEmits<Emits>()
// 親コンポーネントへ更新値の発信
const changedValue = (event: Event) => {
    // セレクトタグのeventとして解釈して値を取得
    const selected = (event.target as HTMLSelectElement).value
    emit('update:value', String(selected))
}
// エラーメッセージのための下マージン設定(エラーメッセージ表示しない時はそもそもマージンを設けない)
const marginForErrorMessage = computed(() =>{
    if (!props.showError) {
        return '0'
    }
    return !!props.errorMessage ? '0' : '15px'
})

/**
 * 未選択オプションのテキスト
 * 選択必須かどうかで文章をかえる
 */
const notSelected = computed(() => {
    return props.value === '0'
})
</script>

<template>
    <div class="c-select--item-select__container" :style="{ marginBottom: marginForErrorMessage }">
        <label class="c-select--item-select__label">
            {{ label }}
            <template v-if="required">
                <RequirementText />
            </template>
        </label>
            <select
                :value="value"
                @change="changedValue"
                id="item-select"
                name=""
                :class="[{ 'u-color--b9': notSelected }, selectClass]"
            >
                <option value="0" disabled>{{ initialOptionText }}</option>
                <option
                    v-for="(item, index) in items"
                    :key="index"
                    :value="item.value">{{ item.text }}</option>
            </select>
            <!-- エラーメッセージのキー設定時のみ表示 -->
            <template v-if="!!errorMessage">
                <ErrorMessage class="u-margin-l-1p" :message="errorMessage" />
            </template>
    </div>
</template>
