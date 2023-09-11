<script setup lang="ts">
import Organization from '@/types/Organization'
import { useForm } from '@inertiajs/vue3'
import InputLabel from '../Form/InputLabel.vue'
import Toggle from '../Form/Toggle.vue'
import notify from '@/utils/notify'

const props = defineProps<{
    organization: Organization
}>()

const form = useForm({
    auto_accept_demo_scheduled: props.organization.auto_accept_demo_scheduled,
    auto_accept_deal_won: props.organization.auto_accept_deal_won,
})

const handleSubmit = () => form.put(route('profile.update'))
</script>

<template>
    <div class="w-128">
        <Toggle
            color="indigo"
            class="mt-5"
            title="Auto Accept SAOs"
            description="You can choose to automatically accept SAOs as soon as they are synced from your Integrations due to their trigger."
            v-model="form.auto_accept_demo_scheduled"
            @update:model-value="handleSubmit"
        />

        <Toggle
            color="indigo"
            class="mt-5"
            title="Auto Accept won Deals"
            description="You can choose to automatically accept won Deals as soon as they are synced from your Integrations."
            v-model="form.auto_accept_deal_won"
            @update:model-value="handleSubmit"
        />
    </div>
</template>
