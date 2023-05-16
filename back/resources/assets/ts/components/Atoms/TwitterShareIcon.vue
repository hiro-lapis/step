<script setup lang="ts">
import { defineProps, ref, computed, onMounted } from 'vue'

// props
const props = defineProps({
    content: { required: false, type: String, default: 'steps', },
    id: { required: true,  type: String, },
    text: { required: true, type: String, },
    hashtags: { required: false, type: String, }
})

// data
// computed
const iconUrl = computed(() => {
    return baseUrl.value + `?url=${process.env.APP_URL}/${props.content}/${props.id}&text=${props.text}`;
})
const baseUrl = computed(() => 'https://twitter.com/share')
</script>

<template>
    <a
        :href="iconUrl"
        target="_blank"
        class="c-btn--twitter-share"
        ><i class="c-icon--small fab fa-twitter"></i>
        twitter
    </a>
</template>
