<script setup lang="ts">
import Card from '@/Components/Card.vue'
import BarChart from './BarChart/BarChart.vue'
import HeaderWithDetailButton from './HeaderWithDetailButton.vue'
</script>

<template>
    <Card>
        <HeaderWithDetailButton
            class="mb-2"
            text="Total Payout by Commission Plan"
        />

        <BarChart />
    </Card>
</template>
