<script setup lang="ts">
import Badge from '@/Components/Badge.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Deal from '@/types/Deal'
import formatDate from '@/utils/Date/formatDate'
import { HandThumbDownIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'

defineEmits<{
    accepted: [id: number]
    declined: [id: number]
}>()

const props = defineProps<{
    deal: Deal
}>()
</script>

<template>
    <div
        class="flex justify-center gap-2 text-gray-500"
        v-if="!props.deal.accepted_at && !props.deal.declined_at"
    >
        <HandThumbUpIcon
            @click="$emit('accepted', deal.id)"
            class="h-6 w-6 cursor-pointer hover:text-green-500"
        />
        <HandThumbDownIcon
            @click="$emit('declined', deal.id)"
            class="h-6 w-6 cursor-pointer hover:text-red-600"
        />
    </div>
    <div
        class="flex justify-center"
        v-else-if="props.deal.accepted_at"
    >
        <Tooltip :text="`This deal was accepted at ${formatDate(props.deal.accepted_at)}.`">
            <Badge
                :text="formatDate(props.deal.accepted_at)"
                color="green"
            />
        </Tooltip>
    </div>
    <div
        class="flex justify-center"
        v-else-if="props.deal.declined_at"
    >
        <Tooltip :text="`This deal was declined at ${formatDate(props.deal.declined_at)}.`">
            <Badge
                :text="formatDate(props.deal.declined_at)"
                color="red"
            />
        </Tooltip>
    </div>
</template>
