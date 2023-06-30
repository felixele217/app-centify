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

    if (event.key === 'Tab') {
        // focusNextElement()
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

    if (props.value.toString().length > 8) {
        return
    }

    if (props.value === 0) {
        emit('set-value', Number(event.key))
    } else {
        emit('set-value', Number(props.value.toString() + event.key))
    }
}

function getCurrencyDisplay(valueInCents: number): string {
    if (valueInCents.toString().length === 1) {
        return `00.0${valueInCents}€`
    }

    if (valueInCents.toString().length === 2) {
        return `00.${valueInCents}€`
    }

    if (valueInCents.toString().length === 3) {
        const firstDigit = valueInCents.toString().slice(0, 1)
        const lastTwoDigits = valueInCents.toString().slice(1)
        return `0${firstDigit}.${lastTwoDigits}€`
    }

    if (valueInCents.toString().length === 4) {
        const firstDigits = valueInCents.toString().slice(0, 2)
        const lastTwoDigits = valueInCents.toString().slice(2)
        return `${firstDigits}.${lastTwoDigits}€`
    }

    const options = {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }
    return `${Number(valueInCents / 100).toLocaleString('de', options)}€`
}
</script>

<template>
    <TextInput
        :textColor="props.value === 0 ? 'text-gray-300' : 'text-gray-900'"
        @keydown.prevent="handleKeyDown"
        type="text"
        :modelValue="getCurrencyDisplay(props.value)"
    />
</template>
