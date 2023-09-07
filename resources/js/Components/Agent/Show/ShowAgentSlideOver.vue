<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import SlideOver, { SlideOverWidth } from '@/Components/SlideOver.vue'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { useForm } from '@inertiajs/vue3'
import Header from '@/Components/Agent/Show/Header.vue'

import { watch } from 'vue'
import CommissionDeepDive from './CommissionDeepDive.vue'
import QuotaDeepDive from './QuotaDeepDive.vue'
import Agent from './Agent.vue'

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
}>()

const emit = defineEmits(['close-show-agent-slide-over'])
</script>

<template>
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="$emit('close-show-agent-slide-over')"
        :title="props.agent?.name ?? ''"
        :description="`Gain a complete overview about the performance of ${props.agent?.name} including detailed commission and quota calculations.`"
        width="max-w-4xl"
    >
        <Agent
            v-if="agent"
            :agent="props.agent"
        />
    </SlideOver>
</template>
