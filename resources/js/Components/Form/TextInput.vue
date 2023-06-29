<script setup lang="ts">
import { onMounted, ref } from 'vue'

defineProps<{
    modelValue: string
    border?: boolean
}>()

defineEmits(['update:modelValue'])

const input = ref<HTMLInputElement | null>(null)

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus()
    }
})

defineExpose({ focus: () => input.value?.focus() })
</script>

<template>
    <input
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        ref="input"
    />
</template>
