<script setup lang="ts">
import FormButtons from '@/Components/Form/FormButtons.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from '@/types/Enum/ContinuationOfPayTimeScopeEnum'
import notify from '@/utils/notify'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import PaidLeaveForm from './PaidLeaveForm.vue'
import SlideOver from '@/Components/SlideOver.vue'

const emit = defineEmits<{
    'close-slide-over': []
}>()

function closeSlideOver() {
    form.reset()

    emit('close-slide-over')
}

const props = defineProps<{
    isOpen: boolean
    agentId?: number
    reason?: AgentStatusEnum
}>()

const form = useForm({
    reason: props.reason as AgentStatusEnum,
    start_date: null as Date | null,
    end_date: null as Date | null,
    continuation_of_pay_time_scope: '' as ContinuationOfPayTimeScopeEnum | '',
    sum_of_commissions: null,
    employed_28_or_more_days: true,
})

watch(
    () => props.isOpen,
    async () => {
        if (props.reason) {
            form.reason = props.reason
        }

        if (props.reason === 'on vacation') {
            form.continuation_of_pay_time_scope = 'last quarter'
        }
    }
)

function submit() {
    form.post(route('agents.paid-leaves.store', props.agentId), {
        onSuccess: () => {
            closeSlideOver()

            notify(
                'Agent updated',
                'Your agent still has the same deals as before, but be aware of errors when trying to sync new data if you changed the email.'
            )
        },
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

<template>
    <SlideOver
        @close-slide-over="closeSlideOver"
        @submit="submit"
        :is-open="props.isOpen"
        title="Manage Paid Leaves"
        description="Add or remove paid leaves for your agents."
        buttonText="Create"
    >
        <div class="space-y-6 pb-5 px-6 pt-3">
            <PaidLeaveForm
                :form="form"
                :agentId="props.agentId"
                @deleted="closeSlideOver"
            />
        </div>
    </SlideOver>
</template>
