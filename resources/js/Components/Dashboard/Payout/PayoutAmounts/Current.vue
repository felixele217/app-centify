<script setup lang="ts">
import { highlightedText, subtleText } from '../../styles'
</script>

<template>
    <div class="mb-4">
        <div class="flex justify-between">
            <p :class="subtleText">Current Payout</p>
            <p :class="subtleText">vs last quarter</p>
        </div>
        <div class="flex justify-between">
            <p :class="highlightedText">15.823€</p>
            <Trend
                type="increase"
                :change="10"
            />
        </div>
    </div>
</template>
