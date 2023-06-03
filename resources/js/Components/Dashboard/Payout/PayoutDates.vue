<script setup lang="ts">
import Card from '../../Card.vue'
import { highlightedText, subtleText } from '../styles'
</script>

<template>
    <Card>
        <div class="mb-8">
            <p :class="subtleText">Next payout date</p>
            <p :class="highlightedText">01.09.2023</p>
        </div>

        <div>
            <p :class="subtleText">Number of payouts this year</p>
            <p :class="highlightedText">3/4</p>
        </div>
    </Card>
</template>
