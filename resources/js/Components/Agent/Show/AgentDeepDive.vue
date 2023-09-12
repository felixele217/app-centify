<script setup lang="ts">
import Header from '@/Components/Agent/Show/Header.vue'
import Agent from '@/types/Agent'
import CommissionDeepDive from './CommissionDeepDive.vue'
import QuotaDeepDive from './QuotaDeepDive.vue'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <Header :agent="props.agent" />

    <div class="mt-14">
        <div class="grid grid-cols-2">
            <CommissionDeepDive :agent="props.agent" />

            <!-- <QuotaDeepDive :agent="props.agent" /> -->
        </div>
    </div>
</template>
