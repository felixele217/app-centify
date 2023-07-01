<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { ref } from 'vue'

const date = ref()
</script>

<template>
    <VueDatePicker
        v-model="date"
        :enable-time-picker="false"
    >
        <template #action-buttons> </template>
    </VueDatePicker>
</template>
