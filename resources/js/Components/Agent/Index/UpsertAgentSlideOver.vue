<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import RadioCards, { RadioCardOption } from '@/Components/RadioCards.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { agentStatusToColor } from '@/utils/Descriptions/agentStatusToColor'
import notify from '@/utils/notify'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import PaidLeaveForm from './PaidLeaveForm.vue'

const emit = defineEmits(['close-slide-over'])

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
    possibleStatuses: Array<AgentStatusEnum>
}>()

const form = useForm({
    name: '',
    email: '',
    base_salary: 0,
    on_target_earning: 0,
    status: 'active' as AgentStatusEnum,
    paid_leave: {
        start_date: null as Date | null,
        end_date: null as Date | null,
        continuation_of_pay_time_scope: '',
        sum_of_commissions: 0,
    },
})

watch(
    () => props.agent,
    async (agent) => {
        if (agent) {
            form.name = agent.name
            form.email = agent.email
            form.base_salary = agent.base_salary ?? 0
            form.on_target_earning = agent.on_target_earning ?? 0
            form.status = agent.status
        }
    }
)

function submit() {
    if (props.agent) {
        form.put(route('agents.update', props.agent.id), {
            onSuccess: () => {
                emit('close-slide-over')
                form.reset()
                notify(
                    'Agent updated',
                    'Your agent still has the same deals as before, but be aware of errors when trying to sync new data if you changed the email.'
                )
            },
        })
    } else {
        form.post(route('agents.store'), {
            onSuccess: () => {
                emit('close-slide-over')
                form.reset()
                notify(
                    'Agent created',
                    "This agent's metrics now count towards your totals.\n You should assign this agent a plan now."
                )
            },
        })
    }
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
            @close="$emit('close-slide-over')"
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
                                    <div class="h-0 flex-1 overflow-y-auto">
                                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-white">{{
                                                    props.agent ? 'Update Agent' : 'Create Agent'
                                                }}</DialogTitle>
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button
                                                        type="button"
                                                        class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                                        @click="$emit('close-slide-over')"
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
                                                    {{
                                                        props.agent
                                                            ? 'Update an existing agent\s data (this will change our calculations).'
                                                            : 'Create a new Agent for your Organization.'
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 flex-col justify-between">
                                            <div class="divide-y divide-gray-200 overflow-y-scroll px-6">
                                                <div class="space-y-6 pb-5 pt-6">
                                                    <div class="leading-6">
                                                        <InputLabel
                                                            for="name"
                                                            value="Name"
                                                            required
                                                        />

                                                        <TextInput
                                                            type="text"
                                                            v-model="form.name"
                                                            name="name"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.name"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="email"
                                                            value="Email"
                                                            required
                                                        />

                                                        <TextInput
                                                            id="email"
                                                            type="text"
                                                            v-model="form.email"
                                                            name="email"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.email"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="base_salary"
                                                            value="Base Salary"
                                                            required
                                                        />

                                                        <CurrencyInput
                                                            :value="form.base_salary"
                                                            @set-value="(value: number) => (form.base_salary = value)"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.base_salary"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="on_target_earning"
                                                            value="On Target Earning (OTE)"
                                                            required
                                                        />

                                                        <CurrencyInput
                                                            :value="form.on_target_earning"
                                                            @set-value="(value: number) => (form.on_target_earning = value)"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.on_target_earning"
                                                        />
                                                    </div>

                                                    <div>
                                                        <RadioCards
                                                            label="Status"
                                                            :options="
                                                                props.possibleStatuses.map((status) => {
                                                                    return {
                                                                        title: status,
                                                                        color: agentStatusToColor[status],
                                                                    }
                                                                })
                                                            "
                                                            @radio-clicked="(option: RadioCardOption<AgentStatusEnum>) => (form.status = option.title)"
                                                            :default="form.status"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.status"
                                                        />
                                                    </div>

                                                    <PaidLeaveForm :form="form" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <FormButtons
                                        :positiveButtonText="props.agent ? 'Save' : 'Create'"
                                        class="pr-4"
                                        @cancel-button-clicked="$emit('close-slide-over')"
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
