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

function customIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    return props.custom_integration_fields.filter(
        (customIntegrationField) => customIntegrationField.name === integrationField
    )[0]
}

function upsertCustomIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    if (customIntegrationField(integrationField)) {
        updateCustomIntegrationField(integrationField)
    } else {
        storeCustomIntegrationField(integrationField)
    }
}

function storeCustomIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    router.post(
        route('custom-integration-fields.store'),
        {
            name: integrationField,
            api_key: demoSetByApiKey.value,
            integration_type: 'pipedrive',
        },
        {
            onSuccess: () => {
                notify('Api key stored!', 'We can now access the value of your custom field.')
            },
        }
    )
}

function updateCustomIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    router.put(
        route('custom-integration-fields.update', customIntegrationField(integrationField)),
        {
            api_key: demoSetByApiKey.value,
        },
        {
            onSuccess: () => {
                notify('Api key updated!', 'We can now access the value of your custom field.')
            },
            onError: () => {
                notify(
                    'Update failed!',
                    'The format of the api key was invalid. It has to be 40 characters long.',
                    false
                )
            },
        }
    )
}

const demoSetByApiKey = ref(customIntegrationField('demo_set_by')?.api_key)
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
            class="ml-5"
            no-top-margin
        />

        <PrimaryButton
            @click="upsertCustomIntegrationField(integrationField)"
            text="Save"
        />
    </div>
</template>
