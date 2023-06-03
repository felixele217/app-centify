<script setup lang="ts">
import Card from '@/Components/Card.vue'
import { subtleText } from '../styles'
</script>

<template>
    <Card>
        <p :class="subtleText">Current Payout by Commission Plan</p>
    </Card>
</template>
