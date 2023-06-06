<script lang="ts">
import tailwindToHex from '@/Components/utils/tailwindToHex'
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

export default {
    name: 'App',
    components: {
        Doughnut,
    },
    data() {
        return {
            data: {
                datasets: [
                    {
                        backgroundColor: [tailwindToHex['bg-violet-950'], tailwindToHex['bg-gray-300']],
                        data: [70, 30],
                    },
                ],
            },
            chartOptions: {
                responsive: true,
                cutout: '80%',
            },
        }
    },
}
</script>

<template>
    <div class="relative flex w-28 items-center justify-center">
        <Doughnut
            :data="data"
            :options="chartOptions"
        />

        <h3 class="absolute -mb-1.5">75%</h3>
    </div>
</template>
