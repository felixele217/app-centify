<script setup lang="ts">
import PayoutAmounts from './PayoutAmounts.vue'
import PayoutDates from './PayoutDates.vue'
import Revenue from './Revenue.vue'
</script>

<template>
    <div class="flex gap-10">
        <PayoutDates class="w-1/3" />

        <PayoutAmounts class="w-1/3" />

        <Revenue class="w-1/3" />
    </div>
</template>
