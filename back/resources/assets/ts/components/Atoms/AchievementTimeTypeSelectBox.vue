<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import RequiredBadge from './RequiredBadge.vue'
import { repositoryKey } from '../../types/common/Injection'
// utilities
const $repositories = inject<Repositories>(repositoryKey)!

// props
const props = defineProps({
    errorKey: { required: false, type: String, default: 'achievement_time_type_id' },
    formId: { required: false, type: String, default: 'achievement_time_type_id' },
    label: { required: false, type: String, default: '' },
    required: { required: false, type: Boolean, default: false },
    selectClass: { required: false, type: String, default: 'c-input--large' },
    value: { required: true, type: Number, },
})

// data
type AchievementTimeType = {
    id: number
    name: string
}
const achievementTimeTypeList = ref<AchievementTimeType[]>([])
const errorMessage = ref('')
const initialState = ref(true)

// computed
// methods
// const isSeleted = (achievementTimeType_id: number) => {
//     return props.value === achievementTimeType_id
// }
const fetchData = async () => {
    await $repositories.common.achievementTimeType()
        .then(response => {
            achievementTimeTypeList.value = response.data
        })
        .catch(() => {
            console.log('error is occured on fetch achievement time types')
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
const initialOptionText = computed(() => props.required ? '選択してください' : '達成目安時間')
const notSelected = computed(() => {
    return props.value === 0
})
// 選択必須設定を条件にバリデーションを行う
const validate = (): boolean => {
    if (!props.required) return true

    if (props.value === 0 || achievementTimeTypeList.value.some(cat => cat.id === props.value)) {
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
    <div class="c-input__container">
        <label class="c-input__label" :for="formId">
            {{ label }}
            <template v-if="required">
                <RequiredBadge />
            </template>
        </label>
            <select
                :value="value"
                @change="changedValue"
                id="achievement_time_type"
                name="achievement_time_type"
                :class="[{ 'u-color--b9': notSelected }, selectClass]"
            >
                <option value="0"
                    :disabled="required">{{ initialOptionText }}</option>
                <option
                    v-for="(achievementTimeType, index) in achievementTimeTypeList"
                    :key="index"
                    :value="achievementTimeType.id">{{achievementTimeType.name}}</option>
            </select>
    </div>
</template>

<style lang="scss" scoped>
@import "../../../sass/foundation/_breakpoints.scss";

.c-input {
    &__container {
        position: relative;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
    }
    &__label {
        display: block;
        padding-left: 5px;
        margin-bottom: 5px;
    }
    &__loading-icon {
        position: absolute;
        top: 44px;
        left: 50%;
    }
    // scssに移すの禁止
    // InputComponentでのみ付与したいスタイリングのため
}

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
// 未選択時に文字色をつける
.notSelected {
    color: #999;
}
</style>
