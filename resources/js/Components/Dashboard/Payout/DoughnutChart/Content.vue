<script setup lang="ts">
import sum from '@/utils/sum'
import tailwindToHex from '@/utils/tailwindToHex'
import { ArcElement, Chart as ChartJS, Tooltip } from 'chart.js'
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'

const props = defineProps<{
    items: Array<{
        value: number
        label: string
    }>
}>()

ChartJS.register(ArcElement, Tooltip)

const colors = [tailwindToHex['primary-500'], tailwindToHex['primary-300'], tailwindToHex['primary-100']].slice(
    0,
    props.items.length
)

const values = computed(() => props.items.map((item) => item.value))

const data = {
    datasets: [
        {
            backgroundColor: [...colors, tailwindToHex['gray-300']],
            data: [...values.value, sum(values.value) >= 100 ? 0 : 100 - sum(values.value)],
            label: [[...props.items.map((item) => item.label), 'Not achieved']],
        },
    ],
}

const chartOptions = {
    responsive: true,
    cutout: '80%',
    plugins: {
        tooltip: {
            filter: function (tooltipItem: any) {
                return tooltipItem.dataIndex !== values.value.length
            },
            callbacks: {
                label: function (context: any) {
                    if (!context) {
                        return ''
                    }

                    const index = context.dataset.data.indexOf(context.raw)

                    return ` ${context.dataset.data[index] * values.value.length}%`
                },
                title: function (context: any) {
                    if (!context[0]) {
                        return ''
                    }

                    const index = context[0].dataset.data.indexOf(context[0].raw)

                    return ` ${context[0].dataset.label[0][index]}`
                },
            },
            displayColors: false,
        },
    },
}
</script>

<template>
    <div class="relative flex w-40 items-center justify-center">
        <Doughnut
            :data="(data as any)"
            :options="chartOptions"
        />

        <h2 class="absolute -mb-1">{{ sum(values).toFixed(0) }}%</h2>
    </div>
</template>
