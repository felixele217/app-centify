<script setup lang="ts">
import Card from '@/Components/Card.vue'
import DealIcon from '@/Components/Icon/DealIcon.vue'

const props = defineProps<{
    openDealCount: number
}>()
</script>

<template>
    <Card class="cursor-pointer hover:bg-gray-50">
        <a
            :href="route('deals.index') + '?scope=open'"
            class="flex h-full flex-col justify-between"
        >
            <div class="flex justify-between">
                <p class="font-semibold">To-Dos</p>

                <DealIcon class="h-8 w-8" />
            </div>
            <div>
                <h2 class="mb-3">{{ props.openDealCount }}</h2>
                <p class="font-semibold text-gray-400">need attention</p>
            </div>
        </a>
    </Card>
</template>
