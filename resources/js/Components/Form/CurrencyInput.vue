<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: number | null
}>()

const emit = defineEmits<{
    'update:modelValue': [newValue: number]
}>()

function handleChange(value: string) {
    const clearedValue = value[0] === '0' ? value.substring(1) : value

    localValue.value = clearedValue + '€'

    if (value[value.length - 1] === '€' && value.includes(',')) {
        emit('update:modelValue', euroValue(value))
    }
}

const localValue = ref<string>(props.modelValue?.toString() || '0,00€')

watch(
    () => localValue.value,
    async () => {
        if (deletedComma(localValue.value)) {
            localValue.value = localValue.value.slice(0, localValue.value.indexOf('00€€') + 1) + ',00€'
        }

        if (deletedLastElement(localValue.value)) {
            localValue.value = localValue.value.slice(0, localValue.value.indexOf(',') - 1) + ',00€'

            emit('update:modelValue', euroValue(localValue.value))
        }

        if (enteredNumber(localValue.value)) {
            localValue.value =
                localValue.value.slice(0, localValue.value.indexOf(',')) +
                localValue.value[localValue.value.length - 1] +
                ',00€'

            emit('update:modelValue', euroValue(localValue.value))
        }

        // remove all non-digits except the ','
        localValue.value = localValue.value.replace(/[^0-9,]/g, '')
    }
)

const deletedLastElement = (value: string) => value[value.length - 1] === '€' && value[value.length - 2] === '0'
const enteredNumber = (value: string) => !isNaN(parseInt(value[value.length - 1])) && value[value.length - 1] !== '0'
const deletedComma = (value: string) => value.includes('€€') && !value.includes(',')
const euroValue = (value: string) =>
    parseInt(value[0] === '0' ? value.slice(1) : value.split(',')[0].replaceAll('.', '')) * 100

const displayValue = () => {
    let clearedValue = localValue.value.slice(0, localValue.value.indexOf(','))

    return `${formatCurrencyWithDots(clearedValue)},00€`
}

function formatCurrencyWithDots(input: string): string {
    if (!input) {
        return '0'
    }

    // Reverse the cleaned string for easier processing
    const reversedChars = input.split('').reverse()

    // Process every 3 characters and join with a dot
    const formattedChunks: string[] = []
    for (let i = 0; i < reversedChars.length; i += 3) {
        formattedChunks.push(
            reversedChars
                .slice(i, i + 3)
                .reverse()
                .join('')
        )
    }

    // Reverse the chunks back and join into a string
    return formattedChunks.reverse().join('.')
}
</script>

<template>
    <div>
        <div class="relative mt-2 rounded-md shadow-sm">
            <input
                type="text"
                class="block w-full rounded-md border-0 py-1.5 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="0.00"
                :value="displayValue()"
                @input="handleChange(($event.target as HTMLInputElement)?.value)"
            />

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-gray-500 sm:text-sm">EUR</span>
            </div>
        </div>
    </div>
</template>
