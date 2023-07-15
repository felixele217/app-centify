<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: GuestLayout })

defineProps<{
    canResetPassword?: boolean
    status?: string
}>()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

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
                name="email"
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
                autocomplete="current-password"
                name="password"
            />

            <InputError
                class="mt-2"
                :message="form.errors.password"
            />
        </div>

        <Checkbox
            name="remember"
            v-model:checked="form.remember"
            label="Remember me"
            class="mt-4 block"
        />

        <div class="mt-4 flex items-center justify-end">
            <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-hover focus:ring-offset-2"
            >
                Forgot your password?
            </Link>

            <PrimaryButton
                class="ml-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Log in
            </PrimaryButton>
        </div>
    </form>
</template>
