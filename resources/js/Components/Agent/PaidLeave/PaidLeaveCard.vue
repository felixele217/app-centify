<script setup lang="ts">
import Badge from '@/Components/Badge.vue'
import Modal from '@/Components/Modal.vue'
import PaidLeave from '@/types/PaidLeave'
import formatDate from '@/utils/Date/formatDate'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps<{
    paidLeave: PaidLeave
}>()

const emit = defineEmits<{
    'deleted-paid-leave': []
}>()

const paidLeaveRange = computed(() => {
    const firstDate = formatDate(new Date(props.paidLeave.start_date))
    const secondDate = props.paidLeave.end_date ? formatDate(new Date(props.paidLeave.end_date)) : null

    return `${firstDate} ${secondDate ? '-' : ''} ${secondDate ? secondDate : ''}`
})

function handleDelete() {
    router.delete(route('agents.paid-leaves.destroy', [props.paidLeave.agent_id, props.paidLeave.id]), {
        onSuccess: () => {
            emit('deleted-paid-leave')
            isDeletingPaidLeave.value = false

            notify(
                'Deleted Paid Leave!',
                `You deleted the recent paid leave where the agent was ${props.paidLeave.reason} from ${formatDate(
                    props.paidLeave.start_date
                )} to ${formatDate(props.paidLeave.end_date)}.`
            )
        },
        preserveState: true,
        preserveScroll: true,
    })
}

const isDeletingPaidLeave = ref<boolean>(false)
</script>

<template>
    <div>
        <Badge
            class="mt-2 flex w-52 justify-end gap-1.5 py-2"
            :text="paidLeaveRange"
            :color="props.paidLeave.reason === 'sick' ? 'purple' : 'yellow'"
            with-delete
            @delete="isDeletingPaidLeave = true"
        />
    </div>

    <Modal
        :is-open="isDeletingPaidLeave"
        title="Delete Paid Leave"
        description="Are you sure you want to delete this Paid Leave? This is irrevocable."
        button-text="Delete"
        is-negative-action
        @close-modal="isDeletingPaidLeave = false"
        @modal-action="handleDelete"
    />
</template>
