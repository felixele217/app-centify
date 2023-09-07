<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import SlideOver, { SlideOverWidth } from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { useForm } from '@inertiajs/vue3'
import Header from '@/Components/Agent/Show/Header.vue'

import { watch } from 'vue'
import CommissionDeepDive from './CommissionDeepDive.vue'
import QuotaDeepDive from './QuotaDeepDive.vue'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <Header :agent="props.agent" />

    <div class="mt-14">
        <div class="grid grid-cols-2">
            <CommissionDeepDive :agent="props.agent" />
            <QuotaDeepDive :agent="props.agent" />
        </div>
    </div>
</template>
