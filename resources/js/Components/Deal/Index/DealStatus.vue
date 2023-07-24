<script setup lang="ts">
import ThumbsDownIcon from '@/Components/Icon/ThumbsDownIcon.vue'
import ThumbsUpIcon from '@/Components/Icon/ThumbsUpIcon.vue'
import Deal from '@/types/Deal'

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
        <ThumbsUpIcon
            @click="$emit('accepted', deal.id)"
            class="h-6 w-6 cursor-pointer hover:text-green-500"
        />
        <ThumbsDownIcon
            @click="$emit('declined', deal.id)"
            class="h-6 w-6 cursor-pointer hover:text-red-600"
        />
    </div>
    <div
        class="flex justify-center"
        v-else-if="props.deal.accepted_at"
    >
        <p class="items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">accepted</p>
    </div>
    <div
        class="flex justify-center"
        v-else-if="props.deal.declined_at"
    >
        <p class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
            declined
        </p>
    </div>
</template>
