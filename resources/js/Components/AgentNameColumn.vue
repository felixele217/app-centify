<script setup lang="ts">
import Agent from '@/types/Agent'
import NameInitials from './NameInitials.vue'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <div class="flex items-center gap-5">
        <NameInitials :name="agent.name" />

        <div>
            <p class="font-medium text-gray-900">{{ agent.name }}</p>
            <p class="font-medium text-gray-500">{{ agent.email }}</p>
        </div>
    </div>
</template>
