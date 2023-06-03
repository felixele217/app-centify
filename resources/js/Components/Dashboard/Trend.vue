<script setup lang="ts">
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'

const props = defineProps<{
    type: 'increase' | 'decrease'
    change: number
}>()
</script>

<template>
    <div
        :class="[
            props.type === 'increase' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
            'inline-flex items-baseline rounded-full px-2.5 py-0.5 text-sm font-medium md:mt-2 lg:mt-0',
        ]"
    >
        <ArrowUpIcon
            v-if="props.type === 'increase'"
            class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-green-500"
            aria-hidden="true"
        />
        <ArrowDownIcon
            v-else
            class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-red-500"
            aria-hidden="true"
        />
        <span class="sr-only"> {{ props.type === 'increase' ? 'Increased' : 'Decreased' }} by </span>
        {{ props.change }}%
    </div>
</template>
