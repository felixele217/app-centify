<script setup lang="ts">
import SecondaryButton from '@/Components/Buttons/SecondaryButton.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import PercentageInput from '@/Components/Form/PercentageInput.vue'
import Select from '@/Components/Form/Select.vue'
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import Deal from '@/types/Deal'
import notify from '@/utils/notify'
import sum from '@/utils/sum'
import { TrashIcon } from '@heroicons/vue/24/outline'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AgentDealShare from './AgentDealShare.vue'

type Partner = {
    id: number | null
    name: string
    deal_percentage: number | null
}

const emit = defineEmits<{
    'close-slide-over': []
}>()

const props = defineProps<{
    isOpen: boolean
    deal: Deal
    agentThatTriggeredDeal: Agent
}>()

const newPartner = () =>
    ({
        name: '',
        id: null,
        deal_percentage: null,
    } as Partner)
const agentNamesToIds = computed(() => usePage().props.agents as Record<string, number>)

const form = useForm({
    partners: existingAgents(),
})

function existingAgents() {
    return props.deal.agents!.length > 1 ? loadExistingAgentsFromSplits() : [newPartner()]
}

watch(
    () => props.isOpen,
    async () => {
        form.partners = existingAgents()
    }
)

function loadExistingAgentsFromSplits() {
    const shareholders =
        props.deal.status === 'won' ? props.deal.deal_won_shareholders : props.deal.demo_scheduled_shareholders

    return Object.values(shareholders!).map((agent) => ({
        name: agent.name,
        id: agent.id,
        deal_percentage: agent.pivot.deal_percentage * 100,
    }))
}

function submit() {
    form.put(route('deals.splits.upsert', props.deal.id), {
        onSuccess: () => {
            closeSlideOver()

            notify('Deal splitted!', 'This deal will now be splitted as specified by you.')
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

// @ts-ignore
const addPartner = () => form.partners.push(newPartner())

function removePartner(index: number) {
    const partners = form.partners

    partners.splice(index, 1)

    form.partners = partners
}

function handlePercentageChange(newValue: number, partnerIndex: number): void {
    form.partners[partnerIndex].deal_percentage = newValue
    const thisPartnersName = form.partners[partnerIndex].name

    const otherPartners = form.partners.filter((partner) => partner.id !== form.partners[partnerIndex].id)
    const equalPercentageForOtherPartners = Math.floor((100 - newValue) / otherPartners.length)

    if (sum(otherPartners.map((partner) => partner.deal_percentage ?? 0)) + newValue > 100) {
        otherPartners.forEach((partner) => (partner.deal_percentage = equalPercentageForOtherPartners))
        form.partners.forEach((partner) =>
            partner.name === thisPartnersName ? newValue : equalPercentageForOtherPartners
        )
    }
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
        <div class="text-gray-700">
            <AgentDealShare
                :agent-name="props.agentThatTriggeredDeal.name"
                :agent-share-percentage="100 - sum(form.partners.map((partner) => partner.deal_percentage || 0))"
            />

            <div
                v-for="partner in form.partners"
                :key="partner.name"
            >
                <AgentDealShare
                    v-if="partner.name"
                    :agent-name="partner.name"
                    :agent-share-percentage="partner.deal_percentage || 0"
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
                    :options="
                        Object.keys(agentNamesToIds).filter(
                            (name) =>
                                name !== props.agentThatTriggeredDeal.name &&
                                !form.partners.map((partner) => partner.name).includes(name)
                        )
                    "
                    v-model="partner.name"
                    @update:model-value="(newName: string) => handlePartnerSelection(newName, index)"
                    no-options-text="All available Agents are already involved..."
                />

                <InputError
                    class="mt-2"
                    :message="(form.errors as Record<string, string>)[`partners.${index}.id`]"
                />
            </div>
            <div>
                <InputLabel
                    for="deal_percentage"
                    value="Shared Percentage"
                    required
                />

                <PercentageInput
                    v-model="partner.deal_percentage"
                    :maximum="100"
                    @update:model-value="(newValue) => handlePercentageChange(newValue, index)"
                />

                <InputError
                    class="mt-2"
                    :message="(form.errors as Record<string, string>)[`partners.${index}.deal_percentage`]"
                />
            </div>
        </div>

        <SecondaryButton
            @click.prevent="addPartner"
            text="+ Add Partner"
        />
    </SlideOver>
</template>
