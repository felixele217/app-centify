<script setup lang="ts">
import euroDisplay from '@/utils/euroDisplay'
import tailwindToHex from '@/utils/tailwindToHex'
import { BarElement, CategoryScale, Chart as ChartJS, LinearScale, Tooltip } from 'chart.js'
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { KICKER_COMMISSION_COLOR, QUOTA_COMMISSION_COLOR } from './config'

const gc = 'bg-primary-300'

export type BarChartContentItem = {
    label: string
    quotaCommission: number
    kickerCommission: number
}

const props = defineProps<{
    items: Array<BarChartContentItem>
}>()

ChartJS.register(Tooltip, BarElement, CategoryScale, LinearScale)

const chartData = computed(() => ({
    labels: props.items.map((item) => item.label),
    datasets: [
        {
            data: props.items.map((item) => item.quotaCommission),
            label: 'Quota Commission',
            backgroundColor: tailwindToHex[QUOTA_COMMISSION_COLOR],
        },
        {
            data: props.items.map((item) => item.kickerCommission),
            label: 'Kicker Commission',
            backgroundColor: tailwindToHex[KICKER_COMMISSION_COLOR],
        },
    ],
}))

const max = Math.max(...props.items.map((item) => item.kickerCommission + item.quotaCommission))

const chartOptions = {
    barThickness: 40,
    borderRadius: 8,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    return ` ${euroDisplay(context.raw)}`
                },
                title: function (context: any) {
                    return ` ${context[0].dataset.label}`
                },
                beforeBody: function (context: any) {
                    return ``
                },
            },
            displayColors: false,
        },
    },
    scales: {
        x: {
            stacked: true,
            grid: {
                display: false,
            },
        },
        y: {
            stacked: true,
            min: 0,
            max: max,
            ticks: {
                stepSize: max / 5,
                callback: function (value: number, index: number, values: Array<number>) {
                    return value !== 0 ? `${euroDisplay(Math.ceil(value / 5_000_00) * 5_000_00)}` : 0
                },
            },
        },
    },
    responsive: true,
}
</script>

<template>
    <div class="h-56">
        <Bar
            id="my-chart-id"
            :options="(chartOptions as any)"
            :data="chartData"
        />
    </div>
</template>
