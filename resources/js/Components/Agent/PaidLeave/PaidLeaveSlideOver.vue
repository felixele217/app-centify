<script setup lang="ts">
import FormButtons from '@/Components/Form/FormButtons.vue'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from '@/types/Enum/ContinuationOfPayTimeScopeEnum'
import notify from '@/utils/notify'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import PaidLeaveForm from './PaidLeaveForm.vue'

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
    })
}
</script>

<template>
    <TransitionRoot
        as="template"
        :show="isOpen"
    >
        <Dialog
            as="div"
            class="relative z-10"
            @close="closeSlideOver"
        >
            <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-auto">
                <div class="absolute inset-0 overflow-auto">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <form
                                    class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl"
                                    @submit.prevent="submit"
                                >
                                    <div class="flex-1 overflow-y-hidden">
                                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-white"
                                                    >Manage Paid Leaves</DialogTitle
                                                >
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button
                                                        type="button"
                                                        class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                                        @click="closeSlideOver"
                                                    >
                                                        <span class="sr-only">Close panel</span>
                                                        <XMarkIcon
                                                            class="h-6 w-6"
                                                            aria-hidden="true"
                                                        />
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-1">
                                                <p class="text-sm text-indigo-300">
                                                    Add or remove paid leaves for your agents
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex h-full flex-1 flex-col justify-between overflow-y-auto">
                                            <div class="h-full divide-y divide-gray-200 px-6">
                                                <div class="space-y-6 pb-5 pt-6">
                                                    <PaidLeaveForm
                                                        :form="form"
                                                        :agentId="props.agentId"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <FormButtons
                                        positiveButtonText="Create"
                                        class="pr-4"
                                        @cancel-button-clicked="closeSlideOver"
                                    />
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
