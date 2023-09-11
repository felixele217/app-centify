<script setup lang="ts">
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
        <div
            v-for="option in props.options"
            class="whitespace-pre-wrap"
        >
            <div
                :key="option.title"
                @click="$emit('option-clicked', option)"
                :class="[
                    option.selected
                        ? 'bg-primary-100 ring-2 ring-primary-600'
                        : 'border border-gray-300 bg-white hover:bg-primary-50',
                    'relative flex cursor-pointer justify-center rounded-md py-2 shadow-sm focus:outline-none',
                ]"
            >
                <span class="flex flex-col">
                    <p :class="[option.selected ? 'text-black' : 'text-gray-900', 'flex text-sm font-medium']">
                        {{ option.title }}
                    </p>
                </span>
            </div>
        </div>
    </div>
</template>
