<script setup lang="ts">
import formatDate from '@/utils/Date/formatDate'
import { MarkedRange } from '@/utils/markedRangesFromRangeObjects'
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { computed, ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    currentDate: Date | null
}>()

const emit = defineEmits<{
    'date-changed': [date: Date]
}>()

const dropdownIsOpen = ref(false)

const selectedYear = ref(new Date().getFullYear())

const months = [
    { label: 'Jan', monthNumber: 1 },
    { label: 'Feb', monthNumber: 2 },
    { label: 'Mar', monthNumber: 3 },
    { label: 'Apr', monthNumber: 4 },
    { label: 'May', monthNumber: 5 },
    { label: 'Jun', monthNumber: 6 },
    { label: 'Jul', monthNumber: 7 },
    { label: 'Aug', monthNumber: 8 },
    { label: 'Sep', monthNumber: 9 },
    { label: 'Oct', monthNumber: 10 },
    { label: 'Nov', monthNumber: 11 },
    { label: 'Dec', monthNumber: 12 },
]

function handleMonthChange(monthNumber: number) {
    dropdownIsOpen.value = false

    emit('date-changed', new Date(selectedYear.value, monthNumber - 1, 1, 0, 0, 0, 0))
}
</script>

<template>
    <Dropdown
        align="left"
        width="64"
        :close-on-content-click="false"
        :is-open="dropdownIsOpen"
        @set-is-open="(state: boolean) => (dropdownIsOpen = state)"
    >
        <template #trigger>
            <TextInput
                readonly
                @keyup.enter="dropdownIsOpen = true"
                class="hover:cursor-pointer focus:outline-transparent"
                :class="props.currentDate ? 'text-gray-900' : 'text-gray-300'"
                :model-value="props.currentDate ? formatDate(props.currentDate) : 'Select a Month...'"
            />
        </template>

        <template #content>
            <div class="rounded-lg px-4 py-4 shadow-md ring-1 ring-gray-900/5">
                <div class="flex items-center justify-between">
                    <ChevronLeftIcon
                        @click="selectedYear--"
                        class="h-7 w-7 cursor-pointer rounded-md stroke-2 p-1 text-slate-500 hover:bg-slate-200"
                    />

                    <p class="text-lg font-medium">{{ selectedYear }}</p>

                    <ChevronRightIcon
                        @click="selectedYear++"
                        class="h-7 w-7 cursor-pointer rounded-md stroke-2 p-1 text-slate-500 hover:bg-slate-200"
                    />
                </div>

                <div class="mt-2 space-y-3">
                    <div class="grid grid-cols-3 items-center justify-between gap-3">
                        <p
                            v-for="month in months"
                            @click="handleMonthChange(month.monthNumber)"
                            class="flex cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            {{ month.label }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </Dropdown>
</template>
