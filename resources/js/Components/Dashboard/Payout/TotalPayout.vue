<script setup lang="ts">
import Card from '@/Components/Card.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import { BanknotesIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

const totalComission = computed(() => euroDisplay(sum(props.agents.map((agent) => agent.commission!))))
</script>

<template>
    <Card class="flex h-full flex-col justify-between">
        <div class="flex justify-between">
            <p class="font-semibold">Total Payout</p>

            <BanknotesIcon class="h-8 w-8" />
        </div>
        <div>
            <h2 class="mb-3">{{ totalComission }}</h2>
            <p class="font-semibold text-gray-400">test</p>
        </div>
    </Card>
</template>
