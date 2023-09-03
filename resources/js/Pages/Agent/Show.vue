<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div class="flex justify-between gap-5">
        <div>
            <h1>{{ agent.name }} Deep Dive</h1>
            <p class="font-semibold text-gray-400">{{ currentScope(timeScopeFromQuery) }}</p>
        </div>

        <Filter :reload-url="route('agents.show', props.agent.id)" />
    </div>
</template>
