<script setup lang="ts">
import DateInput from '@/Components/Form/DateInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    start_date: new Date(), // first day of next month
    target_amount_per_month: 0,
    target_variable: 0,
    payout_frequency: '',
    assigned_agents: [],
})
</script>

<template>
    <div class="mx-20 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
        <h2 class="text-base font-semibold leading-7 text-gray-900">Create Flatrate Commission Plan</h2>
        <p class="mt-1 text-sm leading-6 text-gray-600">
            Receive a fixed percentage of a certain variable such as ARR.
        </p>

        <div class="mt-6 w-1/2">
            <InputLabel
                for="name"
                value="Name"
                required
            />

            <TextInput
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                :border="true"
                name="name"
            />

            <InputError
                class="mt-2"
                :message="form.errors.name"
            />
        </div>

        <div class="mt-6 w-1/2">
            <!-- <InputLabel
                for="start_date"
                value="Start Date"
                required
            /> -->

            <DateInput />

            <!-- <InputError
                class="mt-2"
                :message="form.errors.start_date"
            /> -->
        </div>
    </div>
</template>
