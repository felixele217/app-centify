<script>
import tailwindToHex from '@/Components/utils/tailwindToHex'
import { BarElement, CategoryScale, Chart as ChartJS, LinearScale, Tooltip } from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(Tooltip, BarElement, CategoryScale, LinearScale)

export default {
    name: 'BarChart',
    components: { Bar },
    data() {
        return {
            chartData: {
                labels: ['JUL', 'AUG', 'SEP'],
                datasets: [
                    { data: [1800, 2500, 1500], label: 'Data One', backgroundColor: tailwindToHex['bg-violet-950'] },
                    { data: [700, 400, 200], label: 'Data Two', backgroundColor: tailwindToHex['bg-gray-950'] },
                    { data: [200, 500], label: 'Data Three', backgroundColor: tailwindToHex['bg-violet-700'] },
                ],
            },
            chartOptions: {
                barThickness: 40,
                borderRadius: 8,
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                        },
                        stacked: true,
                        min: 0,
                        max: 4000,
                        ticks: {
                            stepSize: 1000,
                            callback: function (value, index, values) {
                                return value !== 0 ? `${value / 1000}k` : 0
                            },
                        },
                    },
                },
                responsive: true,
            },
        }
    },
}
</script>

<template>
    <Bar
        id="my-chart-id"
        :options="chartOptions"
        :data="chartData"
    />
</template>
