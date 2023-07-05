<script setup lang="ts">
import Card from '@/Components/Card.vue'
import { subtleText } from '../styles'

const props = defineProps<{
    title: string
    icon: any
    amount: string
    subText: string
}>()
</script>

<template>
    <Card class="flex flex-col justify-between">
        <div class="mb-5 flex justify-between">
            <p class="font-semibold">{{ props.title }}</p>
            <component
                class="h-8 w-8"
                :is="props.icon"
            />
        </div>

        <div>
            <h2 class="mb-3">{{ props.amount }}</h2>
            <p :class="subtleText">{{ props.subText }}</p>
        </div>
    </Card>
</template>
