<script setup lang="ts">
const requiredTailwindDeclarations = 'sm:grid-cols-5'

export type CardOptionsOption<T = string> = {
    title: T
    description?: string
    disabled: boolean
}

const emit = defineEmits(['option-clicked'])

const props = defineProps<{
    options: Array<CardOptionsOption>
    optionsPerRow?: number
}>()

function optionClicked(option: CardOptionsOption): void {
    if (!option.disabled) {
        emit('option-clicked', option)
    }
}
</script>

<template>
    <div :class="[`grid grid-cols-1 sm:grid-cols-${props.optionsPerRow || '3'}`, 'mt-4 gap-y-6 sm:gap-x-4']">
        <div
            as="template"
            v-for="option in props.options"
            :key="option.title"
            @click="optionClicked(option)"
            :class="[
                option.disabled ? 'cursor-default bg-gray-300' : 'cursor-pointer bg-white',
                'relative flex justify-center rounded-md border border-gray-300 py-2 shadow-sm focus:outline-none',
            ]"
        >
            <span class="flex flex-col">
                <p :class="[option.disabled ? 'text-gray-500' : 'text-gray-900', 'flex text-sm font-medium']">
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
