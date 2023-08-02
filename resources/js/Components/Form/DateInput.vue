<script setup lang="ts">
import formatDate from '@/utils/Date/formatDate'
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { computed, ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'

export type MarkedRange = {
    start_date: Date
    end_date: Date
    color: 'yellow' | 'green'
}

const props = defineProps<{
    currentDate: Date | null
    markedRanges: Array<MarkedRange>
}>()

function markedDates() {
    return props.markedRanges.map((markedRange) => ({
        dates: [[markedRange.start_date, markedRange.end_date]],
        highlight: {
            color: markedRange.color,
            fillMode: 'light',
        },
    }))
}

const disabledDates = computed(() => [
    ...props.markedRanges.map((markedRange) => datesBetween(markedRange.start_date, markedRange.end_date)),
])

function datesBetween(startDate: Date, endDate: Date): Date[] {
    let datesArray: Date[] = []
    let currentDate = startDate

    while (currentDate <= endDate) {
        datesArray.push(new Date(currentDate))
        currentDate.setDate(currentDate.getDate() + 1)
    }

    return datesArray
}

const selectedColor = ref('indigo')
</script>

<template>
    <Dropdown
        align="left"
        width="48"
    >
        <template #trigger>
            <TextInput
                class="hover:cursor-pointer focus:outline-transparent"
                :class="props.currentDate ? 'text-gray-900' : 'text-gray-300'"
                :model-value="props.currentDate ? formatDate(props.currentDate) : 'Select a Date...'"
            />
        </template>

        <template #content>
            <DatePicker
                :color="selectedColor"
                :model-value="props.currentDate"
                @update:model-value="(newDate: Date) => {
                    newDate.setHours(15)

                    $emit('date-changed', newDate)
                }"
                :attributes="markedDates()"
                :disabledDates="disabledDates"
            />
        </template>
    </Dropdown>
</template>
