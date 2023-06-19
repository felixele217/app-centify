<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import Card from '../../Card.vue'
import Trend from '../Trend.vue'
import { subtleText } from '../styles'
import DoughnutChart from './DoughnutChart/Content.vue'
</script>

<template>
    <Card>
        <div class="mb-2 flex justify-between">
            <div>
                <p :class="subtleText">ARR acquired</p>
                <h2>260.476€</h2>
            </div>
            <PrimaryButton padding="px-2 h-8">
                QTD
                <ChevronDownIcon
                    class="ml-2 h-5 w-5 text-gray-400"
                    aria-hidden="true"
                />
            </PrimaryButton>
        </div>

        <div class="flex justify-between">
            <DoughnutChart />

            <div class="flex flex-col items-end self-end">
                <p
                    class="mb-1"
                    :class="subtleText"
                >
                    vs last quarter
                </p>
                <Trend
                    type="increase"
                    :change="10"
                />
            </div>
        </div>
    </Card>
</template>
