<script setup lang="ts">
import Card from '../Card.vue'
import Trend from './Trend.vue'
import { highlightedText, subtleText } from './styles'
</script>

<template>
    <div class="flex gap-5">
        <Card class="w-1/3">
            <div class="mb-8">
                <p :class="subtleText">Next payout date</p>
                <p :class="highlightedText">01.09.2023</p>
            </div>
            <div>
                <p :class="subtleText">Number of payouts this year</p>
                <p :class="highlightedText">3/4</p>
            </div>
        </Card>

        <Card class="w-1/3">
            <div class="mb-4 flex justify-between gap-10">
                <div>
                    <p :class="subtleText">Current Payout</p>
                    <p :class="highlightedText">15.823€</p>
                </div>
                <div>
                    <p :class="subtleText">vs last quarter</p>
                    <Trend
                        type="increase"
                        :change="10"
                    />
                </div>
            </div>

            <div class="mb-4 flex justify-between gap-10">
                <div>
                    <p :class="subtleText">Total possible payout</p>
                    <p :class="highlightedText">28.312€</p>
                </div>
                <div>
                    <Trend
                        type="decrease"
                        :change="5"
                    />
                </div>
            </div>
            <div>
                <p :class="subtleText">last synchronized</p>
                <p class="text-sm font-bold text-gray-800">11:28 - 09.06.2023</p>
            </div>
        </Card>
        <Card class="w-1/3"> test </Card>
    </div>
</template>
