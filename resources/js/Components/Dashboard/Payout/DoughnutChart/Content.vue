<script lang="ts">
import tailwindToHex from '@/utils/tailwindToHex'
import { ArcElement, Chart as ChartJS, Tooltip } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip)

export default {
    name: 'App',
    props: {
        averageAchievedQuotaAttainment: Number,
    },
    components: {
        Doughnut,
    },
    data() {
        return {
            data: {
                datasets: [
                    {
                        backgroundColor: [tailwindToHex['primary'], tailwindToHex['bg-gray-300']],
                        data: [
                            this.averageAchievedQuotaAttainment!,
                            this.averageAchievedQuotaAttainment! >= 100
                                ? 0
                                : 100 - this.averageAchievedQuotaAttainment!,
                        ],
                    },
                ],
            },
            chartOptions: {
                responsive: true,
                cutout: '80%',
                plugins: {
                    tooltip: {
                        enabled: false,
                    },
                },
            },
        }
    },
}
</script>

<template>
    <div class="relative flex w-40 items-center justify-center">
        <Doughnut
            :data="data"
            :options="chartOptions"
        />

        <h2 class="absolute -mb-1">{{ averageAchievedQuotaAttainment!.toFixed(0) }}%</h2>
    </div>
</template>
