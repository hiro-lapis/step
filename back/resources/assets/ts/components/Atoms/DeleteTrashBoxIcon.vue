<script setup lang="ts">
// props
defineProps({
    id : { require: true, type: Number, },
    clickFunc: { require: false, type: Function, default: () => {} },
    classname: { require: false, type: String, default: '' },
})
// data
</script>

<template>
    <span @click.prevent.stop="clickFunc(id)" :class="classname">
        <span class="c-icon--delete-trash-box material-symbols-outlined">delete</span>
    </span>
</template>
