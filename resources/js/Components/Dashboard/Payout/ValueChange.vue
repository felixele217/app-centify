<script setup lang="ts">
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    value: number | null
    change: string
}>()
</script>

<template>
    <p
        :class="[
            props.value && props.value > 0 ? 'text-green-600' : 'text-red-600',
            'ml-2 flex items-baseline text-xs font-semibold',
        ]"
        v-if="props.value"
    >
        <ArrowUpIcon
            v-if="props.value > 0"
            class="h-3 w-3 flex-shrink-0 self-center text-green-500"
            aria-hidden="true"
        />
        <ArrowDownIcon
            v-else
            class="h-3 w-3 flex-shrink-0 self-center text-red-500"
            aria-hidden="true"
        />
        <span class="-mb-0.5 ml-0.5">{{ props.change }}</span>
    </p>
</template>
