<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import CustomIntegrationField from '@/types/CustomIntegrationField'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
    custom_integration_fields: Array<CustomIntegrationField>
    available_integration_fields: Array<CustomIntegrationFieldEnum>
}>()

function customIntegrationFieldApiKey(integrationField: CustomIntegrationFieldEnum) {
    return (
        props.custom_integration_fields.filter(
            (customIntegrationField) => customIntegrationField.name === integrationField
        )[0]?.api_key || ''
    )
}

function upsertCustomIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    router.post(
        route('custom-integration-fields.store'),
        {
            name: integrationField,
            api_key: demoSetByApiKey.value,
            integration_type: 'pipedrive',
        },
        {
            onSuccess: () => {
                notify('Api Key stored!', 'We can now access the value of your custom field.')
            },
        }
    )
}

const demoSetByApiKey = ref(customIntegrationFieldApiKey('demo_set_by'))
</script>

<template>
    <div
        class="mx-40 flex items-center gap-5 rounded-md p-4"
        v-for="integrationField in props.available_integration_fields"
    >
        <p>
            {{ integrationField }}
        </p>

        <TextInput
            type="text"
            v-model="demoSetByApiKey"
            name="name"
            class="ml-5 mt-0"
        />

        <PrimaryButton
            @click="upsertCustomIntegrationField(integrationField)"
            text="Save"
        />
    </div>
</template>
