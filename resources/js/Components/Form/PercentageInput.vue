<script setup lang="ts">
import TextInput from './TextInput.vue'

const props = defineProps<{
    value: number
}>()

const emit = defineEmits(['set-value'])

function handleChange(newValue: string) {
    if (newValue.includes('%')) {
        emit('set-value', parseInt(newValue.replace('%', '')))
    } else if (newValue.length === 0) {
        emit('set-value', 0)
    } else {
        emit('set-value', parseInt(newValue.slice(0, -1)))
    }
}
</script>

<template>
    <TextInput
        :class="props.value === 0 ? 'text-gray-300' : 'text-gray-900'"
        @update:modelValue="handleChange"
        type="text"
        :modelValue="props.value.toString() + '%'"
    />
</template>
