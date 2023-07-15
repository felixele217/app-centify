<script setup lang="ts">
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'
import { ref } from 'vue'
const requiredColorsDeclaration =
    'bg-green-50 bg-yellow-50 bg-indigo-50 ring-indigo-600 ring-green-600 ring-yellow-600 border-indigo-600 border-yellow-600 border-green-600'

type Option = {
    title: string
    description?: string
    color?: string
}

const props = defineProps<{
    label: string
    options: Array<Option>
    default: string
}>()

const currentOption = ref(props.options.filter((option) => option.title === props.default)[0])

const selected = (option: Option) =>
    currentOption.value.title === option.title && currentOption.value.description === option.description
</script>

<template>
    <RadioGroup v-on:update:model-value="$emit('radio-clicked', currentOption.title)">
        <RadioGroupLabel class="block text-sm font-medium text-gray-700">{{ props.label }}</RadioGroupLabel>
        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-4">
            <RadioGroupOption
                as="template"
                v-for="option in props.options"
                :key="option.title"
                :value="option.title"
            >
                <div
                    @click="currentOption = option"
                    :class="[
                        selected(option) ? 'border-transparent ring-2' : 'border-gray-300',
                        option.color || '',
                        'relative flex cursor-pointer justify-center rounded-md border py-2 shadow-sm focus:outline-none',
                    ]"
                >
                    <span class="flex flex-col">
                        <RadioGroupLabel
                            as="p"
                            class="flex text-sm font-medium text-gray-900"
                            >{{ option.title }}</RadioGroupLabel
                        >
                        <RadioGroupDescription
                            as="span"
                            v-if="option.description"
                            class="mt-1 flex items-center text-xs text-gray-500"
                            >{{ option.description }}</RadioGroupDescription
                        >
                    </span>
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>
