<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import Card from '@/Components/Card.vue'
import useCustomFieldRefs from '@/Components/CustomField/Composable/useCustomFieldRefs'
import TextInput from '@/Components/Form/TextInput.vue'
import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import Integration from '@/types/Integration'
import customField from '@/utils/CustomField/customField'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'

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
const apiKeyRefs = useCustomFieldRefs(props.available_custom_field_names, props.integration.custom_fields!)
</script>

<template>
    <div class="w-2/3">
        <Card>
            <h2>How to create and add Custom Integration Fields in {{ props.integration.name }}</h2>

            <div class="mt-3">
                <a
                    :href="`https://${props.integration.subdomain}.pipedrive.com/settings/fields`"
                    class="link"
                    target="_blank"
                >
                    1. Open your Custom Integration Fields
                </a>
            </div>
            <div class="mt-2 flex gap-0.5">
                <p class="">2.</p>
                <div class="">
                    You should see a green
                    <span class="font-bold"> 'Add custom field'</span>
                    button.
                    <br />
                    In pipedrive, create the fields which types (e.g. Person field) and names (e.g. 'demo_set_by') match
                    the ones of the fields below.
                </div>
            </div>
            <p class="mt-2">3. After creating the keys, click the option dots and copy&paste their API keys.</p>
        </Card>

        <Card class="mt-6">
            <h3>Your Integration API Keys</h3>

            <div
                class="flex items-center gap-5 rounded-md py-2"
                v-for="(customFieldName, index) in props.available_custom_field_names"
                :key="index"
            >
                <p class="whitespace-nowrap">{{ customFieldName }}:</p>
                <TextInput
                    type="text"
                    v-model="apiKeyRefs[customFieldName]"
                    class="ml-3"
                    no-top-margin
                />
                <PrimaryButton
                    @click="upsertCustomField(customFieldName)"
                    text="Save"
                />
            </div>
        </Card>
    </div>
</template>
