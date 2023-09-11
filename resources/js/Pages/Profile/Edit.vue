<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import Card from '@/Components/Card.vue'
import AutoAcceptForm from '@/Components/Profile/AutoAcceptForm.vue'
import Organization from '@/types/Organization'

const props = defineProps<{
    organization: Organization
    mustVerifyEmail?: boolean
    status?: string
}>()
</script>

<template>
    <Head title="Profile" />

    <div class="py-5">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <Card>
                <AutoAcceptForm :organization="props.organization" />
            </Card>
            <Card>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </Card>
            <Card>
                <UpdatePasswordForm class="max-w-xl" />
            </Card>
            <Card>
                <DeleteUserForm class="max-w-xl" />
            </Card>
        </div>
    </div>
</template>
