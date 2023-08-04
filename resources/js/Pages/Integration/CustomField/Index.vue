<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import useCustomFieldRefs from '@/Components/CustomIntegrationField/Composables/useCustomFieldRefs'
import TextInput from '@/Components/Form/TextInput.vue'
import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import Integration from '@/types/Integration'
import customField from '@/utils/CustomField/customField'
import notify from '@/utils/notify'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
    integration: Integration
    available_custom_field_names: Array<CustomFieldEnum>
}>()

function upsertCustomField(customFieldName: CustomFieldEnum) {
    const currentCustomField = customField(props.integration.custom_fields, customFieldName)

    if (currentCustomField) {
        updateCustomField(currentCustomField)
    } else {
        storeCustomField(customFieldName)
    }
}

function storeCustomField(customFieldName: CustomFieldEnum) {
    router.post(
        route('integrations.custom-fields.store', props.integration.id),
        {
            name: customFieldName,
            api_key: apiKeyRefs.value[customFieldName],
            integration_type: props.integration.name,
        },
        {
            onSuccess: () => {
                notify('Api key stored!', 'We can now access the value of your custom field.')
            },
            onError: () => {
                notify(
                    'Store failed!',
                    'The format of the api key was invalid. It has to be 40 characters long.',
                    false
                )
            },
        }
    )
}

function updateCustomField(customField: CustomField) {
    router.put(
        route('integrations.custom-fields.update', [customField.integration_id, customField.id]),
        {
            api_key: apiKeyRefs.value[customField.name],
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
const apiKeyRefs = useCustomFieldRefs(props.available_custom_field_names, props.integration.custom_fields)

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
            v-for="(customFieldName, index) in props.available_custom_field_names"
            :key="index"
        >
            <p class="whitespace-nowrap">{{ customFieldName }}:</p>

            <TextInput
                type="text"
                v-model="apiKeyRefs[customFieldName]"
                class="ml-5"
                no-top-margin
            />

            <PrimaryButton
                @click="upsertCustomField(customFieldName)"
                text="Save"
            />
        </div>
    </div>
</template>
