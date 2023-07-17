<script setup lang="ts">
import TextInput from './TextInput.vue'

const props = defineProps<{
    value: number
}>()

const emit = defineEmits(['set-value'])

function handleKeyDown(event: any) {
    if (!['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'Backspace', 'Tab', '-'].includes(event.key)) {
        return
    }

    if (event.key === '-') {
        emit('set-value', -props.value)
        return
    }

    if (event.key === 'Backspace') {
        if (props.value < 0 && props.value.toString().length === 2) {
            emit('set-value', 0)
            return
        }

        emit('set-value', Number(props.value.toString().slice(0, -1)))
        return
    }

    if (props.value >= 100) {
        return
    }

    if (Number(props.value.toString() + event.key) >= 100) {
        emit('set-value', 100)
        return
    }

    if (props.value === 0) {
        emit('set-value', Number(event.key))
    } else {
        emit('set-value', Number(props.value.toString() + event.key))
    }
}

const percentageDisplay = (percentage: number) => `${Number(percentage)}%`
</script>

<template>
    <TextInput
        :class="props.value === 0 ? 'text-gray-300' : 'text-gray-900'"
        @keydown.prevent="handleKeyDown"
        type="text"
        :modelValue="percentageDisplay(props.value)"
    />
</template>
