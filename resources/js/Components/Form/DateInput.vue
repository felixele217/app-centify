<script setup lang="ts">
import formatDate from '@/utils/Date/formatDate'
import { MarkedRange } from '@/utils/markedRangesFromRangeObjects'
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { computed, ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'

const props = defineProps<{
    currentDate: Date | null
    markedRanges?: Array<MarkedRange>
}>()

function vCalendarMarkedRanges() {
    return props.markedRanges?.map((markedRange) => ({
        dates: [[markedRange.start_date, markedRange.end_date]],
        highlight: {
            color: markedRange.color,
            fillMode: 'light',
        },
    }))
}

const disabledDates = computed(() =>
    props.markedRanges?.map((markedRange) => [markedRange.start_date, markedRange.end_date])
)

const selectedColor = ref('indigo')
const dropdownIsOpen = ref(false)
</script>

<template>
    <Dropdown
        align="left"
        width="48"
        :close-on-content-click="false"
        :is-open="dropdownIsOpen"
        @set-is-open="(state: boolean) => (dropdownIsOpen = state)"
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
                    if (newDate) {
                        newDate.setHours(15)
                    }

                    dropdownIsOpen = false

                    $emit('date-changed', newDate)
                }"
                :disabledDates="disabledDates"
                :attributes="vCalendarMarkedRanges()"
            />
        </template>
    </Dropdown>
</template>
