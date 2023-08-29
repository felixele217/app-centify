<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const emit = defineEmits<{
    'close-upsert-agent-slide-over': []
}>()

function closeSlideOver() {
    form.reset()

    emit('close-upsert-agent-slide-over')
}

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
}>()

const form = useForm({
    name: '',
    email: '',
    base_salary: null as number | null,
    on_target_earning: null as number | null,
})

watch(
    () => props.agent,
    async (agent) => {
        if (agent) {
            form.name = agent.name
            form.email = agent.email
            form.base_salary = agent.base_salary ?? null
            form.on_target_earning = agent.on_target_earning ?? null
        }
    }
)

function submit() {
    if (props.agent) {
        form.put(route('agents.update', props.agent.id), {
            onSuccess: () => {
                closeSlideOver()

                notify(
                    'Agent updated',
                    'Your agent still has the same deals as before, but be aware of errors when trying to sync new data if you changed the email.'
                )
            },
        })
    } else {
        form.post(route('agents.store'), {
            onSuccess: () => {
                closeSlideOver()

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
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="closeSlideOver"
        @submit="submit"
        :title="props.agent ? 'Update Agent' : 'Create Agent'"
        :button-text="props.agent ? 'Save' : 'Create'"
        :description="
            props.agent
                ? 'Update an existing agent\s data (this will change our calculations).'
                : 'Create a new Agent for your Organization.'
        "
    >
        <div class="leading-6">
            <InputLabel
                for="name"
                value="Full Name"
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
            <div class="flex gap-1">
                <InputLabel
                    for="email"
                    value="Work Email"
                    required
                />

                <InfoIcon
                    hover-text="This email will be used to synchronize agent data from your integrations.
                Make sure it matches the mail of the agent in your CRM system."
                />
            </div>

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
                value="Annual Base Salary"
                required
            />

            <CurrencyInput v-model="form.base_salary" />

            <InputError
                class="mt-2"
                :message="form.errors.base_salary"
            />
        </div>
        <div>
            <div class="flex gap-1">
                <InputLabel
                    for="on_target_earning"
                    value="Annual On Target Earning (OTE)"
                    required
                />

                <InfoIcon
                    hover-text="On-target earning (OTE) is the expected total pay, including base salary and variable salary, if performance targets are met."
                />
            </div>
            <CurrencyInput v-model="form.on_target_earning" />

            <InputError
                class="mt-2"
                :message="form.errors.on_target_earning"
            />
        </div>

        <p
            class="text-sm text-gray-600"
            v-if="form.on_target_earning && form.base_salary && form.on_target_earning > form.base_salary"
        >
            Hence, {{ form.name }} has a total variable pay of
            <span class="font-semibold">{{ euroDisplay(form.on_target_earning - form.base_salary) }}.</span>
        </p>
    </SlideOver>
</template>
