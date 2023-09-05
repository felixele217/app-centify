<script lang="ts">
import tailwindToHex from '@/utils/tailwindToHex'
import { ArcElement, Chart as ChartJS, Tooltip } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip)

export default {
    name: 'App',
    props: {
        quotaAttainment: Number,
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
                        data: [this.quotaAttainment!, this.quotaAttainment! >= 100 ? 0 : 100 - this.quotaAttainment!],
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

        <h2 class="absolute -mb-1">{{ quotaAttainment!.toFixed(0) }}%</h2>
    </div>
</template>
