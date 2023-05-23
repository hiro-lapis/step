<script setup lang="ts">
// props
const props = defineProps({
    stepId : { require: true, type: Number, },
    clickFunc: { require: false, type: Function, default: () => {} },
    classname: { require: false, type: String, default: '' },
})
</script>

<template>
    <span @click.prevent.stop="clickFunc()" :class="classname">
        <span class="c-icon--edit material-symbols-outlined">edit_square</span>
    </span>
</template>
