<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import Card from '../../Card.vue'
import { highlightedText, subtleText } from '../styles'
</script>

<template>
    <Card>
        <div class="mb-8 flex justify-between">
            <div>
                <p :class="subtleText">ARR acquired</p>
                <p :class="highlightedText">260.476€</p>
            </div>
            <PrimaryButton text="QTD" />
        </div>

        <div>
            <p :class="subtleText">Number of payouts this year</p>
            <p :class="highlightedText">3/4</p>
        </div>
    </Card>
</template>
