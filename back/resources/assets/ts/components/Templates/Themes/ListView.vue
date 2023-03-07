<script setup lang="ts">

console.log('now using base view!!')

// utilities(store,router)
// props
// data
// emits
// computed
// watch
// methods
</script>

<template>
    <BaseView>
        <template v-slot:content>
            <h1>ログイン後の一覧</h1>
            <div>
                <router-link to="/good-morning">GoodMorning</router-link>
            </div>
            <div>
                <router-link to="/login">Login</router-link>
            </div>
        </template>
    </BaseView>
</template>
