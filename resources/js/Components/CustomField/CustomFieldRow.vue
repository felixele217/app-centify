<script setup lang="ts">
import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import Integration from '@/types/Integration'
import customField from '@/utils/CustomField/customField'
import { customFieldToDescription } from '@/utils/Descriptions/customFieldToDescription'
import notify from '@/utils/notify'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import SecondaryButton from '../Buttons/SecondaryButton.vue'
import TextInput from '../Form/TextInput.vue'

const props = defineProps<{
    customFieldName: CustomFieldEnum
    integration: Integration
}>()

const vFocus = {
    mounted: (el: HTMLInputElement) => el.focus(),
}

const handleBlur = () =>
    setTimeout(() => {
        if (!isUpsertingApiKey.value) {
            if (apiKey.value !== customField(props.integration.custom_fields!, props.customFieldName)?.api_key) {
                apiKey.value = customField(props.integration.custom_fields!, props.customFieldName)?.api_key
            }

            isEditing.value = false
        }
    }, 100)

function upsertCustomField(customFieldName: CustomFieldEnum) {
    isUpsertingApiKey.value = true

    const currentCustomField = customField(props.integration.custom_fields!, customFieldName)

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
            api_key: apiKey.value,
            integration_type: props.integration.name,
        },
        {
            onSuccess: () => {
                notify('Api key stored!', 'We can now access the value of your custom field.')
                isEditing.value = false
            },
            onError: () => {
                notify(
                    'Store failed!',
                    'The format of the api key was invalid. It has to be 40 characters long.',
                    false
                )
            },
            onFinish: () => (isUpsertingApiKey.value = false),
        }
    )
}

function updateCustomField(customField: CustomField) {
    router.put(
        route('integrations.custom-fields.update', [customField.integration_id, customField.id]),
        {
            api_key: apiKey.value,
        },
        {
            onSuccess: () => {
                notify('Api key updated!', 'We can now access the value of your custom field.')
                isEditing.value = false
            },
            onError: () => {
                notify(
                    'Update failed!',
                    'The format of the api key was invalid. It has to be 40 characters long.',
                    false
                )
            },
            onFinish: () => (isUpsertingApiKey.value = false),
        }
    )
}

const isUpsertingApiKey = ref<boolean>(false)
const isEditing = ref<boolean>(false)
const apiKey = ref<string>(customField(props.integration.custom_fields!, props.customFieldName)?.api_key)
</script>

<template>
    <div class="flex items-center gap-4 rounded-md py-2 text-sm">
        <p class="whitespace-nowrap">{{ customFieldName }} {{ customFieldToDescription[customFieldName] }}:</p>

        <div
            v-if="!isEditing"
            class="flex w-full items-center justify-between gap-5"
            @click="isEditing = true"
        >
            <p
                v-if="apiKey"
                class="hover:cursor-pointer"
            >
                {{ apiKey }}
            </p>

            <div
                class="flex items-center gap-2"
                v-else
            >
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" />
                <p>Please enter your api key!</p>
            </div>

            <SecondaryButton text="Edit" />
        </div>

        <div
            v-else
            class="flex w-full items-center justify-between gap-5"
        >
            <TextInput
                type="text"
                v-model="apiKey"
                v-focus
                @blur="handleBlur"
                @keyup.enter="upsertCustomField(customFieldName)"
                no-top-margin
            />

            <SecondaryButton
                @click="upsertCustomField(customFieldName)"
                text="Save"
            />
        </div>
    </div>
</template>
