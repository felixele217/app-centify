<script setup lang="ts">
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'
import { CheckCircleIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'

type Option = {
    title: string
    description?: string
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
                        selected(option) ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300',
                        'relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none',
                    ]"
                >
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <RadioGroupLabel
                                as="span"
                                class="block text-sm font-medium text-gray-900"
                                >{{ option.title }}</RadioGroupLabel
                            >
                            <RadioGroupDescription
                                as="span"
                                class="mt-1 flex items-center text-xs text-gray-500"
                                >{{ option.description }}</RadioGroupDescription
                            >
                        </span>
                    </span>

                    <CheckCircleIcon
                        :class="[!selected(option) ? 'invisible' : '', 'h-5 w-5 text-indigo-600']"
                        aria-hidden="true"
                    />

                    <span
                        :class="[
                            selected(option) ? 'border border-indigo-600' : 'border-transparent',
                            'pointer-events-none absolute -inset-px rounded-lg',
                        ]"
                        aria-hidden="true"
                    />
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>
