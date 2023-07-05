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
    <Card>
        <div class="mb-5 flex justify-between">
            <p class="font-semibold">{{ props.title }}</p>
            <component
                :is="props.icon"
            />
        </div>

        <h2 class="mb-3">{{ props.amount }}</h2>

        <p :class="subtleText">{{ props.subText }}</p>
    </Card>
</template>
