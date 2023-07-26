<script setup lang="ts">
import { InformationCircleIcon } from '@heroicons/vue/24/outline'
import Tooltip from '../Tooltip.vue'

const props = withDefaults(
    defineProps<{
        hoverText: string
        size?: string
    }>(),
    {
        size: 'w-5 h-5',
    }
)
</script>

<template>
    <Tooltip :text="props.hoverText">
        <InformationCircleIcon :class="props.size" />
    </Tooltip>
</template>
