<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <div class="flex justify-between">
        <div>
            <div class="flex gap-10">
                <div class="mr-20">
                    <h1>{{ props.agent.name }}</h1>
                    <p class="mt-1 text-gray-500">{{ props.agent.email }}</p>
                </div>

                <div>
                    <p class="mb-0.5 text-sm text-gray-500">Base Salary</p>
                    <p class="text-lg font-semibold text-gray-700">
                        {{ euroDisplay(props.agent.base_salary) }}
                    </p>
                </div>

                <div>
                    <p class="mb-0.5 text-sm text-gray-500">On Target Earning</p>
                    <p class="text-lg font-semibold text-gray-700">
                        {{ euroDisplay(props.agent.on_target_earning) }}
                    </p>
                </div>
            </div>
        </div>

        <div>
            <Filter :reload-url="route('agents.show', props.agent.id)" />
        </div>
    </div>
</template>
