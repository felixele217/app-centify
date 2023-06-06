<script setup lang="ts">
import Trend from '../../Trend.vue';
import { highlightedText, subtleText } from '../../styles'
</script>

<template>
    <div class="mb-4">
        <p :class="subtleText">Total possible payout</p>
        <div class="flex justify-between">
            <p :class="highlightedText">28.312€</p>
            <Trend
                type="decrease"
                :change="5"
            />
        </div>
    </div>
</template>
