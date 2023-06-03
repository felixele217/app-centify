<script setup lang="ts">
import Card from '../Card.vue'
import { highlightedText, subtleText } from './styles'
</script>

<template>
    <Card>
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
</template>
