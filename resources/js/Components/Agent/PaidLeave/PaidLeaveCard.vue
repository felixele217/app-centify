<script setup lang="ts">
import Badge from '@/Components/Badge.vue'
import PaidLeave from '@/types/PaidLeave'
import formatDate from '@/utils/Date/formatDate'
import { computed } from 'vue'

const props = defineProps<{
    paidLeave: PaidLeave
}>()

const paidLeaveRange = computed(() => {
    const firstDate = formatDate(new Date(props.paidLeave.start_date))
    const secondDate = props.paidLeave.end_date ? formatDate(new Date(props.paidLeave.end_date)) : null

    return `${firstDate} ${secondDate ? '-' : ''} ${secondDate ? secondDate : ''}`
})


</script>

<template>
    <div>
        <Badge
            :text="paidLeaveRange"
            :color="props.paidLeave.reason === 'sick' ? 'purple' : 'yellow'"
        />
    </div>
</template>
