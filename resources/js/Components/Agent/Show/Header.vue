<script setup lang="ts">
import Card from '@/Components/Card.vue'
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import euroDisplay from '@/utils/euroDisplay'
import queryParamValue from '@/utils/queryParamValue'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <Card class="flex justify-between">
        <div>
            <div class="flex gap-10">
                <div class="mr-10">
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
    </Card>
</template>
