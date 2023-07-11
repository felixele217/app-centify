<script setup lang="ts">
import Card from '@/Components/Card.vue'

const props = defineProps<{
    title: string
    icon: any
    amount: string
    subText: string
    link?: string
}>()
</script>

<template>
    <Card :class="props.link ? 'cursor-pointer hover:bg-gray-50' : 'cursor-default'">
        <a
            :href="props.link || '#'"
            class="flex h-full flex-col justify-between"
        >
            <div class="mb-5 flex justify-between">
                <p class="font-semibold">{{ props.title }}</p>
                <component
                    class="h-8 w-8"
                    :is="props.icon"
                />
            </div>
            <div>
                <h2 class="mb-3">{{ props.amount }}</h2>
                <p class="font-semibold text-gray-400">{{ props.subText }}</p>
            </div>
        </a>
    </Card>
</template>
