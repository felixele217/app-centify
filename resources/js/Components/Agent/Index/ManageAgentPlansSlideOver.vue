<script setup lang="ts">
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import notify from '@/utils/notify'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const emit = defineEmits<{
    'close-slide-over': []
}>()

function closeSlideOver() {
    form.reset()

    emit('close-slide-over')
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
        title="Manage Plans"
        description="You can manage which plans will affect this agent's commission."
    >
        <p v-for="active_plan_name in props.agent?.active_plans_names">{{ active_plan_name }}</p>
    </SlideOver>
</template>
