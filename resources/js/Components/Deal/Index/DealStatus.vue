<script setup lang="ts">
import Deal from '@/types/Deal'
import formatDate from '@/utils/Date/formatDate'
import { CheckIcon, HandThumbDownIcon, HandThumbUpIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'
import AcceptedOrRejectedBadge from './AcceptedOrRejectedBadge.vue'

defineEmits<{
    accepted: [id: number]
    rejected: [id: number]
}>()

const props = defineProps<{
    deal: Deal
}>()

const agentThatTriggeredDeal = computed(() => props.deal.a_e ?? props.deal.s_d_r)
</script>

<template>
    <div
        class="flex justify-center text-gray-500"
        v-if="!agentThatTriggeredDeal!.pivot.accepted_at && !props.deal.active_rejection?.created_at"
    >
        <div>
            <CheckIcon
                @click="$emit('accepted', deal.id)"
                class="h-8 w-8 cursor-pointer rounded-full p-1 hover:bg-green-100 hover:text-green-700"
            />
        </div>

        <div>
            <XMarkIcon
                @click="$emit('rejected', deal.id)"
                class="h-8 w-8 cursor-pointer rounded-full p-1 hover:bg-red-100 hover:text-red-800"
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
        :text="`This deal was accepted at ${formatDate(agentThatTriggeredDeal!.pivot.accepted_at)}.`"
        :acted_at="agentThatTriggeredDeal!.pivot.accepted_at"
        color="green"
        v-else-if="agentThatTriggeredDeal!.pivot.accepted_at"
    />
</template>
