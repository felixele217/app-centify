<script setup lang="ts">
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'
import formatDate from '@/utils/formatDate'

const props = defineProps<{
    date: Date | null
}>()

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
                :class="props.date ? 'text-gray-900' : 'text-gray-300'"
                :model-value="props.date ? formatDate(props.date) : 'Select a Date...'"
            />
        </template>

        <template #content>
            <DatePicker
                :color="selectedColor"
                :model-value="props.date"
                @update:model-value="(newDate: Date) => $emit('date-changed', newDate)"
            />
        </template>
    </Dropdown>
</template>
