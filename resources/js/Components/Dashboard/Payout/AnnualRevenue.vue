<script setup lang="ts">
import Card from '../../Card.vue'
import { subtleText } from '../styles'
import DoughnutChart from './DoughnutChart/Content.vue'
</script>

<template>
    <Card class="flex justify-between">
        <div>
            <p class="mb-3 font-semibold">Quota Attainment</p>
            <h1 class="mb-8">ARR acquired</h1>
            <h2 class="mb-3">125.000€</h2>
            <p :class="subtleText">250.000€</p>
        </div>

        <DoughnutChart />
    </Card>
</template>
