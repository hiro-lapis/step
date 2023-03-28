<script setup lang="ts">
import { computed, ref } from 'vue'

// props
const props = defineProps({
    currentPage: { required: true, type: Number, }, // 現在のページ
    lastPage: { required: true, type: Number, },
    total: { required: true, type: Number, },
})
interface Emits {
    (e: 'change:page', page: number): void
}
const emit = defineEmits<Emits>()

// data
const pageRange = ref(5)

// methods
const emitPage = (page: number) => {
    if (props.currentPage === page) {
        return;
    }
    emit("change:page", page);
}
const isActive = (page: number) =>  {
    return page === props.currentPage
}
const calRange = (start, end) => {
    const range: Array<number> = []
    for (let i = start; i <= end; i++) {
        range.push(i)
    }
    return range
}
// computed
const pagination = computed(() => {
    let start = 0;
    let end = 0;
    start = minPage.value
    end = maxPage.value
    return calRange(start, end);
})
const minPage = computed(() => {
    // 総ページ数が5未満の時
    if (props.lastPage < pageRange.value) {
        return 1;
    } else if (props.currentPage <= halfOfRange.value) {
        // 総ページ数が5より上だが現在のページが3未満の時
        return 1;
    } else {
        // 総ページ数が5より上,現在のページが3より上で,,,
        // かつ現在のページと最終ページの差が5未満は最終ページ-4
        return props.lastPage - 4;
    }
})
const maxPage = computed(() => {
    // 総ページ数が5未満の時
    if (props.lastPage < pageRange.value) {
        return props.lastPage;
    } else if (props.currentPage <= halfOfRange.value) {
        // 総ページ数が5より上だが現在のページ数が3未満の時
        return pageRange.value;
    } else if (props.lastPage - props.currentPage < pageRange.value) {
        // 総ページ数が5より上で現在のページ数が3以上
        // かつ最終ページと現在のページ差が5未満
        return props.lastPage;
    } else {
        // 総ページ数が5より上で現在のページ数が3以上
        // かつ最終ページと現在のページ差が5以上
        return props.currentPage + 2;
    }
})

// 次のページボタンの表示
const showPrevius = computed(() => {
    return props.currentPage > 1;
})
// 前のページボタンの表示
const showNext = computed(() => {
    return props.currentPage < props.lastPage;
})
// 現在のページが総ページより5(一度に表示するページ番号数)以上か判定
const isLargerThanPageRange = computed(() => {
    return props.lastPage - props.currentPage >= 5;
})
const halfOfTotal = computed(() => {
    return Math.ceil(props.lastPage / 2);
})
const halfOfRange = computed(() => {
    return Math.ceil(pageRange.value / 2);
})
</script>

<template>
    <ul class="c-pagenation">
        <template v-if="showPrevius">
            <li
                class="c-pagenation__item c-pagenation__first"
                @click="emitPage(currentPage - 1)"
            >
                '&lt;'
            </li>
        </template>
        <template :key="page" v-for="page in pagination">
            <li
                class="c-pagenation__item"
                @click="emitPage(page)"
                :class="{ active: isActive(page) }"
            >
                {{ page }}
            </li>
        </template>
        <template v-if="showNext">
            <li
                class="c-pagenation__item c-pagenation__last"
                @click="emitPage(currentPage + 1)"
            >
                >
            </li>
        </template>
    </ul>
</template>

<style lang="scss" scoped>
.c-pagenation {
    display: flex;
    &__item {
        border: 1px solid #ddd;
        padding: 6px 12px;
        text-align: center;
        cursor: pointer;
        & + .c-pagenation__item {
            border-left: none;
        }
    }
}
.active {
    font-weight: bold;
    cursor: default;
}
</style>
