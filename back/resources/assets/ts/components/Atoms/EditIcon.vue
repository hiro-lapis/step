<script setup lang="ts">
import { useRouter } from 'vue-router'

// utilities
const router = useRouter()
// props
const props = defineProps({
    stepId : { require: true, type: Number, },
})
// data
const moveToEditPage = () => {
    router.push({ name: 'steps-edit', params: { id: props.stepId } })
}
</script>

<template>
    <span @click.prevent.stop="moveToEditPage()" class="c-step-card__edit-icon">
        <i class="c-icon--edit fas fa-edit"></i>
    </span>
</template>
