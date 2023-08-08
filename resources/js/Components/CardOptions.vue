<script setup lang="ts">
import Tooltip from './Tooltip.vue'

const requiredTailwindDeclarations = 'sm:grid-cols-5 sm:grid-cols-4 sm:grid-cols-3'

export type CardOptionsOption<T = string> = {
    title: T
    description?: string
    selected: boolean
}

defineEmits<{
    'option-clicked': [option: CardOptionsOption<string>]
}>()

const props = defineProps<{
    options: Array<CardOptionsOption>
    optionsPerRow?: number
}>()
</script>

<template>
    <div :class="[`grid grid-cols-1 sm:grid-cols-${props.optionsPerRow || '3'}`, 'mt-2 gap-y-6 sm:gap-x-4']">
        <Tooltip
            :text="option.description || ''"
            v-for="option in props.options"
            placement="top"
            class="whitespace-pre-wrap"
        >
            <div
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
                </span>
            </div>
        </Tooltip>
    </div>
</template>
