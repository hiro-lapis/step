<script setup lang="ts">
import { defineProps, ref, computed } from 'vue'

// props
const props = defineProps({
    content: { required: false, type: String, default: 'steps', },
    id: { required: true,  type: String, },
    text: { required: true, type: String, },
    hashtags: { required: false, type: String, }
})

// data
const appUrl = ref(process.env.MIX_APP_URL)

// computed
const iconUrl = computed(() => {
    return baseUrl.value + `?url=${appUrl.value}/${props.content}/${props.id}&text=${props.text}&hashtags=${props.hashtags}`;
})
const baseUrl = computed(() => 'https://twitter.com/intent/tweet')
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
