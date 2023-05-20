<script setup lang="ts" scoped>
import { useRoute } from 'vue-router'
import StepFormView from '../../Organisms/Steps/StepForm.vue'
// utilities
const route = useRoute()
const stepId = Number(route.params.id)
</script>
<template>
    <StepFormView :mode="'edit'" :stepId="stepId" />
</template>
