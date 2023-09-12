<script setup lang="ts">
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import AgentDeepDive from './AgentDeepDive.vue'

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
}>()

defineEmits(['close-show-agent-slide-over'])
</script>

<template>
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="$emit('close-show-agent-slide-over')"
        :title="props.agent?.name ?? ''"
        :description="`Gain a complete overview about the performance of ${props.agent?.name} including detailed commission and quota calculations.`"
    >
        <AgentDeepDive
            v-if="props.agent"
            :agent="props.agent"
        />
    </SlideOver>
</template>
