<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import useFieldsRef from '@/Components/CustomIntegrationField/Composables/useFieldsRef'
import TextInput from '@/Components/Form/TextInput.vue'
import CustomIntegrationField from '@/types/CustomIntegrationField'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'
import customIntegrationField from '@/utils/CustomIntegrationField/customIntegrationField'
import notify from '@/utils/notify'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

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

const pipedriveSubdomain = computed(() => usePage().props.auth.user.organization.active_integrations.pipedrive)
</script>

<template>
    <div class="w-2/3">
        <h2>Custom Integration Fields</h2>

        <p class="mt-2">
            To streamline all integration processes, we require you to create and use the following custom fields in
            your CRM.
        </p>

        <p class="mt-6">
            To create the custom fields go to <br />
            <span class="font-bold">Pipedrive > Company Settings > Company > Datafields</span>
            or
            <a
                :href="`https://${pipedriveSubdomain}.pipedrive.com/settings/fields`"
                class="link"
                target="_blank"
            >
                click this link.
            </a>
        </p>

        <p class="mt-6">
            You should see a green <span class="font-bold"> 'Add custom field button'. </span>
            <br />
            Below you can see the expected names and types of the fields you have to create.
        </p>

        <p class="mt-6">After creating the keys, click the option dots and copy&paste their API keys.</p>

        <div
            class="mt-6 flex items-center gap-5 rounded-md py-2"
            v-for="(integrationFieldName, index) in props.available_integration_field_names"
            :key="index"
        >
            <p class="whitespace-nowrap">{{ integrationFieldName }}:</p>

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
