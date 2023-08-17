<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
    modelValue: number | null
    maximum?: number
}>()

const emit = defineEmits<{
    'update:modelValue': [newValue: number]
}>()

watch(
    () => props.modelValue,
    async (value) => {
        if (!value) {
            return
        }

        if (value < 0) {
            emit('update:modelValue', -value)
        }

        if (props.maximum && value > props.maximum) {
            emit('update:modelValue', props.maximum)
        }
    }
)
</script>

<template>
    <div>
        <div class="relative mt-2 rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <span class="text-gray-500 sm:text-sm">%</span>
            </div>

            <input
                type="number"
                class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6"
                placeholder="0"
                :value="props.modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement)?.valueAsNumber)"
            />

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-gray-500 sm:text-sm">Percent</span>
            </div>
        </div>
    </div>
</template>
