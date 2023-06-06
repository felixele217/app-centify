<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
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
            <PrimaryButton padding="px-2 h-8">
                QTD
                <ChevronDownIcon
                    class="ml-2 h-5 w-5 text-gray-400"
                    aria-hidden="true"
                />
            </PrimaryButton>
        </div>

        <div>
            <p :class="subtleText">Number of payouts this year</p>
            <p :class="highlightedText">3/4</p>
        </div>
    </Card>
</template>
