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

const dropdownIsOpen = ref(false)

const selectedYear = ref(new Date().getFullYear())
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
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <ChevronLeftIcon
                        @click="selectedYear--"
                        class="h-7 w-7 rounded-md stroke-2 p-1 text-slate-500 hover:bg-slate-200"
                    />
                    <p class="text-lg font-medium">{{ selectedYear }}</p>
                    <ChevronRightIcon
                        @click="selectedYear++"
                        class="h-7 w-7 rounded-md stroke-2 p-1 text-slate-500 hover:bg-slate-200"
                    />
                </div>

                <div class="mt-2 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Jan
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Feb
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Mar
                        </p>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Apr
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            May
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Jun
                        </p>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Jul
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Aug
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Sep
                        </p>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Oct
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Nov
                        </p>
                        <p
                            class="flex w-1/3 cursor-pointer items-center justify-center rounded-md px-4 py-1 ring-2 ring-slate-200 hover:bg-slate-100"
                        >
                            Dec
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </Dropdown>
</template>
