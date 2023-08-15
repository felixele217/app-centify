<script setup lang="ts">
import SecondaryButton from '@/Components/Buttons/SecondaryButton.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import PercentageInput from '@/Components/Form/PercentageInput.vue'
import Select from '@/Components/Form/Select.vue'
import SlideOver from '@/Components/SlideOver.vue'
import Deal from '@/types/Deal'
import notify from '@/utils/notify'
import sum from '@/utils/sum'
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AgentDealShare from './AgentDealShare.vue'

const emit = defineEmits<{
    'close-slide-over': []
}>()

const props = defineProps<{
    isOpen: boolean
    deal: Deal
}>()

const newPartner = {
    name: '',
    id: null as number | null,
    shared_percentage: 0,
}

const agentNamesToIds = computed(() => usePage().props.agents as Record<string, number>)

const agentIdsToNames = computed(() => {
    const idsToNames: Record<number, string> = {}

    for (const name in agentNamesToIds.value) {
        idsToNames[agentNamesToIds.value[name]] = name
    }

    return idsToNames
})

const form = useForm({
    partners: props.deal.splits!.length ? loadExistingAgentsFromSplits() : [newPartner],
})

watch(
    () => props.isOpen,
    async () => {
        form.partners = props.deal.splits!.length ? loadExistingAgentsFromSplits() : [newPartner]
    }
)

function loadExistingAgentsFromSplits() {
    return props.deal.splits!.map((split) => ({
        name: agentIdsToNames.value[split.agent_id],
        id: split.agent_id,
        shared_percentage: split.shared_percentage,
    }))
}

function submit() {
    form.post(route('deals.splits.store', props.deal.id), {
        onSuccess: () => {
            closeSlideOver()

            notify('Deal splitted', 'This deal will now be splitted as specified by you.')
        },
    })
}

function closeSlideOver() {
    form.reset()

    emit('close-slide-over')
}

function handlePartnerSelection(name: string, index: number): void {
    form.partners[index].name = name
    form.partners[index].id = agentNamesToIds.value[name]
}

const addPartner = () => form.partners.push(newPartner)

const removePartner = (index: number) => {
    const partners = form.partners

    partners.splice(index, 1)

    form.partners = partners
}
</script>

<template>
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="closeSlideOver"
        @submit="submit"
        :title="`Split ${props.deal.title}`"
        button-text="Split"
        description="You can split deals to accomodate the profit of more than one agent for a deal."
    >
        <div class="px-6">
            <div class="my-5 text-gray-700">
                <AgentDealShare
                    :agent-name="props.deal.agent!.name"
                    :agent-share-percentage="100 - sum(form.partners.map((partner) => partner.shared_percentage || 0))"
                />

                <div v-for="partner in form.partners">
                    <AgentDealShare
                        v-if="partner.name && partner.shared_percentage"
                        :agent-name="partner.name"
                        :agent-share-percentage="partner.shared_percentage"
                    />
                </div>
            </div>

            <div
                class="mb-8 space-y-4"
                v-for="(partner, index) in form.partners"
            >
                <div>
                    <div class="flex justify-between">
                        <InputLabel
                            for="partner"
                            :value="'Partner ' + (index + 1)"
                            required
                        />

                        <TrashIcon
                            class="h-6 w-6 cursor-pointer rounded-full p-1 text-gray-700 hover:bg-gray-100 hover:text-black"
                            @click="() => removePartner(index)"
                        />
                    </div>

                    <Select
                        :options="Object.keys(agentNamesToIds).filter(name => name !== deal.agent!.name && ! form.partners.map(partner => partner.name).includes(name))"
                        :selected-option="partner.name"
                        @option-selected="(newName) => handlePartnerSelection(newName, index)"
                        no-options-text="All available Agents are already involved..."
                    />

                    <InputError
                        class="mt-2"
                        :message="(form.errors as Record<string, string>)[`partners.${index}.id`]"
                    />
                </div>
                <div>
                    <InputLabel
                        for="shared_percentage"
                        value="Shared Percentage"
                        required
                    />

                    <PercentageInput v-model="partner.shared_percentage" />

                    <InputError
                        class="mt-2"
                        :message="(form.errors as Record<string, string>)[`partners.${index}.shared_percentage`]"
                    />
                </div>
            </div>

            <SecondaryButton @click.prevent="addPartner">
                <PlusIcon class="mr-0.5 h-4 w-4 stroke-2" />
                Add Partner
            </SecondaryButton>
        </div>
    </SlideOver>
</template>
