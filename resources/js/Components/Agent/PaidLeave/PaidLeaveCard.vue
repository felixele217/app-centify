<script setup lang="ts">
import Badge from '@/Components/Badge.vue'
import PaidLeave from '@/types/PaidLeave'
import formatDate from '@/utils/Date/formatDate'

const props = defineProps<{
    paidLeave: PaidLeave
}>()
</script>

<template>
    <div>
        <Badge
            :text="formatDate(new Date(paidLeave.start_date)) + ' - ' + formatDate(new Date(paidLeave.end_date))"
            :color="paidLeave.reason === 'sick' ? 'green' : 'yellow'"
        />
    </div>
</template>
