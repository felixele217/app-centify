<script setup lang="ts">
// @ts-ignore
import { DatePicker } from 'v-calendar'
import 'v-calendar/style.css'
import { ref } from 'vue'
import Dropdown from '../Dropdown/Dropdown.vue'
import TextInput from './TextInput.vue'

const props = defineProps<{
    date: Date
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
                class="hover:cursor-pointer"
                :model-value="props.date.toLocaleDateString('DE-de')"
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
