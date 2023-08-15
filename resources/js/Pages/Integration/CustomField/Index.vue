<script setup lang="ts">
import Card from '@/Components/Card.vue'
import CustomFieldRow from '@/Components/CustomField/CustomFieldRow.vue'
import SyncIntegrationButton from '@/Components/Integrations/SyncIntegrationButton.vue'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import Integration from '@/types/Integration'
import hasMissingCustomField from '@/utils/Integration/hasMissingCustomField'
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    integration: Integration
    available_custom_field_names: Array<CustomFieldEnum>
}>()

const goBack = () => window.history.back()
</script>

<template>
    <div class="flex gap-10">
        <Card class="w-3/4">
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
                <p>2.</p>
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

        <div class="flex w-1/4 items-start justify-end">
            <ArrowUturnLeftIcon
                @click="goBack"
                class="h-10 w-10 cursor-pointer rounded-full p-1.5 text-gray-400 hover:bg-gray-200 hover:text-gray-500"
                aria-hidden="true"
            />
        </div>
    </div>

    <div class="flex gap-10">
        <Card class="mt-6 w-3/4">
            <div class="mb-8 flex justify-between">
                <h3>Your Integration API Keys</h3>
                <SyncIntegrationButton
                    text="Test & Sync"
                    :redirect-url="route('integrations.index')"
                    :disabled="hasMissingCustomField(props.integration)"
                    :integrationName="props.integration.name"
                />
            </div>
            <CustomFieldRow
                v-for="(customFieldName, index) in props.available_custom_field_names"
                :key="index"
                :custom-field-name="customFieldName"
                :integration="props.integration"
            />
        </Card>

        <div class="w-1/4" />
    </div>
</template>
