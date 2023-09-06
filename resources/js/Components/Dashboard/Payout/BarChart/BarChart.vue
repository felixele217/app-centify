<script setup lang="ts">
import LegendItemVue from '../LegendItem.vue'
import Content, { BarChartContentItem } from './Content.vue'
import { KICKER_COMMISSION_COLOR, QUOTA_COMMISSION_COLOR } from './config'

const props = defineProps<{
    items: Array<BarChartContentItem>
}>()

const legendItems = [
    {
        label: 'Quota Commission',
        color: QUOTA_COMMISSION_COLOR,
    },
    {
        label: 'Kicker Commission',
        color: KICKER_COMMISSION_COLOR,
    },
]
</script>

<template>
    <div class="mb-5 flex gap-4">
        <LegendItemVue
            v-for="item in legendItems"
            :color="item.color"
            :name="item.label"
        />
    </div>

    <Content :items="props.items" />
</template>
