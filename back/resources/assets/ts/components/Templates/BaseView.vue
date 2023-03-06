<script setup lang="ts">
import Loading from '../Atoms/Loading.vue'
import NotifyMessage from '../Atoms/NotifyMessage.vue'
import Header from '../Header.vue'
import Footer from '../Footer.vue'
</script>

<template>
    <Loading />
    <NotifyMessage />
    <Header />
        <slot name="content">
        </slot>
    <Footer />
</template>
