<script setup lang="ts">
import formatCurrencyWithDots from '@/utils/formatCurrencyWithDots'
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

    if (value[value.length - 1] === '€' && value.includes(',00')) {
        console.log(0, euroValue(localValue.value))

        emitUpdate()
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

            console.log(1, localValue.value, euroValue(localValue.value))
            emitUpdate()
        }

        if (addedZero(localValue.value)) {
            localValue.value = localValue.value.substring(0, localValue.value.indexOf(',')) + '0,00€'

            console.log(2, localValue.value, euroValue(localValue.value))
            emitUpdate()
        }

        if (enteredNumber(localValue.value)) {
            localValue.value =
                localValue.value.slice(0, localValue.value.indexOf(',')) +
                localValue.value[localValue.value.length - 1] +
                ',00€'

            console.log(3, localValue.value, euroValue(localValue.value))
            emitUpdate()
        }

        // remove all non-digits except the ','
        localValue.value = localValue.value.replace(/[^0-9,]/g, '')
    }
)

function emitUpdate() {
    const value = euroValue(localValue.value)

    if (!isNaN(value)) {
        emit('update:modelValue', value)
    } else {
        emit('update:modelValue', 0)
    }
}

const deletedLastElement = (value: string) =>
    value[value.length - 1] === '€' && value[value.length - 2] === '0' && value[value.length - 3] !== '€'
const enteredNumber = (value: string) => !isNaN(parseInt(value[value.length - 1])) && value[value.length - 1] !== '0'
const deletedComma = (value: string) => value.includes('€€') && !value.includes(',')
const addedZero = (value: string) => value.includes('€0€')
const euroValue = (value: string) =>
    parseInt(value[0] === '0' ? value.slice(1) : value.split(',')[0].replaceAll('.', '')) * 100

const displayValue = () => {
    let clearedValue = localValue.value.slice(0, localValue.value.indexOf(','))

    return `${formatCurrencyWithDots(clearedValue)},00€`
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
