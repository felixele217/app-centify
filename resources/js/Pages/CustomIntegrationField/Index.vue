<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import useFieldsRef from '@/Components/CustomIntegrationField/Composables/useFieldsRef'
import TextInput from '@/Components/Form/TextInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import CustomIntegrationField from '@/types/CustomIntegrationField'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'
import customIntegrationField from '@/utils/CustomIntegrationField/customIntegrationField'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    custom_integration_fields: Array<CustomIntegrationField>
    available_integration_field_names: Array<CustomIntegrationFieldEnum>
}>()

function upsertCustomIntegrationField(integrationField: CustomIntegrationFieldEnum) {
    if (customIntegrationField(props.custom_integration_fields, integrationField)) {
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
            api_key: apiKeyRefs.value[integrationField],
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
        route(
            'custom-integration-fields.update',
            customIntegrationField(props.custom_integration_fields, integrationField)
        ),
        {
            api_key: apiKeyRefs.value[integrationField],
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
const apiKeyRefs = useFieldsRef(props.available_integration_field_names, props.custom_integration_fields)
</script>

<template>
    <div class="">
        <PageHeader
            title="Custom API Keys"
            :description="'This needs to be set to the API key for this data field. Go to Pipedrive > Company Settings > Company > Datafields.\nYou can hover over the data field of concern and click on the options symbol to copy the key.'"
        />
        <div
            class="mr-96 flex items-center gap-5 rounded-md py-2"
            v-for="(integrationFieldName, index) in props.available_integration_field_names"
            :key="index"
        >
            <p>{{ integrationFieldName }}:</p>

            <TextInput
                type="text"
                v-model="apiKeyRefs[integrationFieldName]"
                class="ml-5"
                no-top-margin
            />

            <PrimaryButton
                @click="upsertCustomIntegrationField(integrationFieldName)"
                text="Save"
            />
        </div>
    </div>
</template>
