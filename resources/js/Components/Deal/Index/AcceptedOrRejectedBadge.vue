<script setup lang="ts">
import Badge from '@/Components/Badge.vue'
import Tooltip from '@/Components/Tooltip.vue'
import formatDate from '@/utils/Date/formatDate'

const props = defineProps<{
    acted_at?: string
    color: 'red' | 'green'
    text: string
}>()
</script>

<template>
    <div class="flex justify-center whitespace-nowrap">
        <Tooltip :text="props.text">
            <Badge
                :text="formatDate(props.acted_at)"
                :color="props.color"
            />
        </Tooltip>
    </div>
</template>
