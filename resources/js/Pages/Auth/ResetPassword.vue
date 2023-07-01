<script lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'

export default {
    layout: GuestLayout,
}
</script>

<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{
    email: string
    token: string
}>()

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Reset Password" />

    <form @submit.prevent="submit">
        <div>
            <InputLabel
                for="email"
                value="Email"
            />

            <TextInput
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
            />

            <InputError
                class="mt-2"
                :message="form.errors.email"
            />
        </div>

        <div class="mt-4">
            <InputLabel
                for="password"
                value="Password"
            />

            <TextInput
                id="password"
                type="password"
                v-model="form.password"
                required
                autocomplete="new-password"
            />

            <InputError
                class="mt-2"
                :message="form.errors.password"
            />
        </div>

        <div class="mt-4">
            <InputLabel
                for="password_confirmation"
                value="Confirm Password"
            />

            <TextInput
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
            />

            <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
            />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Reset Password
            </PrimaryButton>
        </div>
    </form>
</template>
