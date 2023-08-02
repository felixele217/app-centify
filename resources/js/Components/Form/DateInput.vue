<script setup lang="ts">
import formatDate from '@/utils/Date/formatDate'
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { computed, ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'

const props = defineProps<{
    currentDate: Date | null
    sickDates: Array<[Date, Date]>
    vacationDates: Array<[Date, Date]>
}>()

function markedDates(sickDates: Array<[Date, Date]>, vacationDates: Array<[Date, Date]>) {
    return [
        ...sickDates.map((sickDates) => ({
            dates: [sickDates],
            highlight: {
                color: 'green',
                fillMode: 'light',
            },
        })),
        ...vacationDates.map((vacationDates) => ({
            dates: [vacationDates],
            highlight: {
                color: 'yellow',
                fillMode: 'light',
            },
        })),
    ]
}

const disabledDates = computed(() => [
    // @ts-ignore
    ...props.sickDates,
    ...props.vacationDates,
])

const selectedColor = ref('indigo')
</script>

<template>
    <Dropdown
        align="left"
        width="48"
        content-classes=""
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
                :attributes="markedDates(props.sickDates, props.vacationDates)"
                :disabledDates="disabledDates"
            />
        </template>
    </Dropdown>
</template>
