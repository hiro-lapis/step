<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue'
import { Repositories } from '../../apis/repositoryFactory'
import RequiredBadge from './RequiredBadge.vue'
import { repositoryKey } from '../../types/common/Injection'
import { AchievementTimeType } from '../../types/AchievementTimeType'
import RequirementText from './RequirementText.vue'

// utilities
const $repositories = inject<Repositories>(repositoryKey)!
// props
const props = defineProps({
    errorKey: { required: false, type: String, default: 'achievement_time_type_id' },
    formId: { required: false, type: String, default: 'achievement_time_type_id' },
    label: { required: false, type: String, default: '' },
    required: { required: false, type: Boolean, default: false },
    initialOptionText: { required: false, type: String, default: '選択してください' },
    selectClass: { required: false, type: String, default: 'c-input--large' },
    value: { required: true, type: Number, },
})
// data
const achievementTimeTypeList = ref<AchievementTimeType[]>([])
const errorMessage = ref('')
const initialState = ref(true)

// methods
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
                <RequirementText />
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
// scssに移すの禁止
// InputComponentでのみ付与したいスタイリングのため
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
}
</style>
