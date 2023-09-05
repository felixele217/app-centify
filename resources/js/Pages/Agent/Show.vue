<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import euroDisplay from '@/utils/euroDisplay'
import queryParamValue from '@/utils/queryParamValue'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div class="w-216">
        <div class="flex justify-between gap-5">
            <h1>{{ agent.name }}</h1>
            <div>
                <Filter :reload-url="route('agents.show', props.agent.id)" />
                <p class="font-semibold text-gray-400">{{ currentScope(timeScopeFromQuery) }}</p>
            </div>
        </div>
        <div class="mt-5 grid w-80 grid-cols-2 space-y-0.5 text-gray-600">
            <p class="font-semibold text-gray-900">Base Salary:</p>
            <p class="text-right tabular-nums">{{ euroDisplay(agent.base_salary) }}</p>
            <p class="font-semibold text-gray-900">On Target Earning:</p>
            <p class="text-right tabular-nums">{{ euroDisplay(agent.on_target_earning) }}</p>
        </div>
    </div>
</template>
