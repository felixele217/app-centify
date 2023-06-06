<script setup lang="ts">
import { highlightedText } from '../../styles'
import Plan from '../Plan.vue';
</script>

<template>
    <div class="my-5 flex justify-between">
        <p :class="highlightedText">10.748</p>
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
                color="bg-violet-700"
                name="Plan C"
            />
        </div>
    </div>
</template>
