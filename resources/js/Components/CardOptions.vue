<script setup lang="ts">
const requiredTailwindDeclarations = 'sm:grid-cols-5 sm:grid-cols-4'

export type CardOptionsOption<T = string> = {
    title: T
    description?: string
    selected: boolean
}

const emit = defineEmits(['option-clicked'])

const props = defineProps<{
    options: Array<CardOptionsOption>
    optionsPerRow?: number
}>()
</script>

<template>
    <div :class="[`grid grid-cols-1 sm:grid-cols-${props.optionsPerRow || '3'}`, 'mt-4 gap-y-6 sm:gap-x-4']">
        <div
            as="template"
            v-for="option in props.options"
            :key="option.title"
            @click="$emit('option-clicked', option)"
            :class="[
                option.selected
                    ? 'border-indigo-200 bg-indigo-100 hover:bg-indigo-50'
                    : 'border-gray-300 bg-white hover:bg-indigo-50',
                'relative flex cursor-pointer justify-center rounded-md border py-2 shadow-sm focus:outline-none',
            ]"
        >
            <span class="flex flex-col">
                <p :class="[option.selected ? 'text-indigo-900' : 'text-gray-900', 'flex text-sm font-medium']">
                    {{ option.title }}
                </p>
                <span
                    v-if="option.description"
                    class="mt-1 flex items-center text-xs text-gray-500"
                    >{{ option.description }}</span
                >
            </span>
        </div>
    </div>
</template>
