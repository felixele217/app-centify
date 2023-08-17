<script setup lang="ts">
import { CurrencyInputVue } from 'vue-simple-currency'
const props = defineProps<{
    modelValue: number | null
}>()

defineEmits<{
    'update:modelValue': [newValue: number]
}>()
</script>

<template>
    <div class="relative mt-2 rounded-md shadow-sm">
        <CurrencyInputVue
            class="block w-full rounded-md border-0 py-1.5 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6"
            v-model="props.modelValue"
            @update:model-value="(newValue) => $emit('update:modelValue', newValue)"
        />

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <span class="text-gray-500 sm:text-sm">EUR</span>
        </div>
    </div>
</template>
