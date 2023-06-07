<script setup lang="ts">
import Plan from '../Plan.vue'
</script>

<template>
    <div class="mb-5 flex justify-between">
        <h2>10.748</h2>
        <div class="flex gap-2">
            <Plan
                color="bg-violet-950"
                name="Plan A"
            />
            <Plan
                color="bg-gray-950"
                name="Plan B"
            />
            <Plan
                color="bg-violet-primary"
                name="Plan C"
            />
        </div>
    </div>
</template>
