<script setup lang="ts">
import Deal from '@/types/Deal'
import formatDate from '@/utils/Date/formatDate'
import { HandThumbDownIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import AcceptedOrRejectedBadge from './AcceptedOrRejectedBadge.vue'

defineEmits<{
    accepted: [id: number]
    rejected: [id: number]
}>()

const props = defineProps<{
    deal: Deal
}>()
</script>

<template>
    <div
        class="flex justify-center gap-2 text-gray-500"
        v-if="!props.deal.accepted_at && !props.deal.active_rejection?.created_at"
    >
        <div>
            <HandThumbUpIcon
                @click="$emit('accepted', deal.id)"
                class="h-6 w-6 cursor-pointer hover:text-green-500"
            />
        </div>

        <div>
            <HandThumbDownIcon
                @click="$emit('rejected', deal.id)"
                class="h-6 w-6 cursor-pointer hover:text-red-600"
            />
        </div>
    </div>

    <AcceptedOrRejectedBadge
        :text="`This deal was rejected ${props.deal.active_rejection?.is_permanent ? 'permanently' : 'temporarily'}
        on ${formatDate(props.deal.active_rejection?.created_at)} due to: '${props.deal.active_rejection?.reason}'`"
        :acted_at="props.deal.active_rejection!.created_at"
        color="red"
        v-else-if="props.deal.active_rejection?.created_at"
    />

    <AcceptedOrRejectedBadge
        :text="`This deal was accepted at ${formatDate(props.deal.accepted_at)}.`"
        :acted_at="props.deal.accepted_at"
        color="green"
        v-else-if="props.deal.accepted_at"
    />
</template>
