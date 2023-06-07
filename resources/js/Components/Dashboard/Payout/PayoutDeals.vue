<script setup lang="ts">
import Card from '@/Components/Card.vue'
import HeaderWithDetailButton from './HeaderWithDetailButton.vue'
import TopDeals from './TopDeals.vue'
</script>

<template>
    <Card>
        <HeaderWithDetailButton text="Deals to focus on" />

        <h2 class="mb-5">Top 5</h2>

        <TopDeals />
    </Card>
</template>
