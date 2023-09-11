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
    demo_scheduled_deal_percentage: number | null
    deal_won_deal_percentage: number | null
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
        demo_scheduled_deal_percentage: null,
        deal_won_deal_percentage: null,
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

function loadExistingAgentsFromSplits(): Array<Partner> {
    const shareholderIds = [
        ...new Set([
            ...Object.values(props.deal.demo_scheduled_shareholders!).map((agent) => agent.id),
            ...Object.values(props.deal.deal_won_shareholders!).map((agent) => agent.id),
        ]),
    ]

    return shareholderIds.map((agentId) => ({
        name:
            Object.values(props.deal.demo_scheduled_shareholders!).filter(
                (demoScheduledAgent) => demoScheduledAgent.id === agentId
            )[0]?.name ||
            Object.values(props.deal.demo_scheduled_shareholders!).filter(
                (demoScheduledAgent) => demoScheduledAgent.id === agentId
            )[0]?.name,
        id: agentId,
        demo_scheduled_deal_percentage:
            Object.values(props.deal.demo_scheduled_shareholders!).filter(
                (demoScheduledAgent) => demoScheduledAgent.id === agentId
            )[0]?.pivot.deal_percentage || null,
        deal_won_deal_percentage:
            Object.values(props.deal.deal_won_shareholders!).filter((dealWonAgent) => dealWonAgent.id === agentId)[0]
                ?.pivot.deal_percentage || null,
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

function handlePercentageChange(newValue: number, forPercentage: 'SAO' | 'ARR', partnerIndex: number): void {
    const attributeName = forPercentage === 'SAO' ? 'demo_scheduled_deal_percentage' : 'deal_won_deal_percentage'

    form.partners[partnerIndex][attributeName] = newValue

    const thisPartnersName = form.partners[partnerIndex].name

    const otherPartners = form.partners.filter((partner) => partner.id !== form.partners[partnerIndex].id)
    const equalPercentageForOtherPartners = Math.floor((100 - newValue) / otherPartners.length)

    if (sum(otherPartners.map((partner) => partner[attributeName] ?? 0)) + newValue > 100) {
        otherPartners.forEach((partner) => (partner[attributeName] = equalPercentageForOtherPartners))
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
                :demo-scheduled-percentage="
                    100 - sum(form.partners.map((partner) => partner.demo_scheduled_deal_percentage || 0))
                "
                :deal-won-percentage="100 - sum(form.partners.map((partner) => partner.deal_won_deal_percentage || 0))"
                :with-arr="!!props.deal.a_e" />

            <div
                v-for="partner in form.partners"
                :key="partner.name"
            >
                <AgentDealShare
                    v-if="partner.name"
                    :agent-name="partner.name"
                    :demo-scheduled-percentage="partner.demo_scheduled_deal_percentage || 0"
                    :deal-won-percentage="partner.deal_won_deal_percentage || 0"
                    :with-arr="!!props.deal.a_e"
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

            <div v-if="!props.deal.a_e">
                <InputLabel
                    for="deal_percentage"
                    value="SAO Percentage"
                    required
                />

                <PercentageInput
                    v-model="partner.demo_scheduled_deal_percentage"
                    :maximum="100"
                    @update:model-value="(newValue) => handlePercentageChange(newValue, 'SAO', index)"
                />

                <InputError
                    class="mt-2"
                    :message="(form.errors as Record<string, string>)[`partners.${index}.demo_scheduled_deal_percentage`]"
                />
            </div>

            <div
                class="flex justify-between gap-5"
                v-else
            >
                <div>
                    <InputLabel
                        for="deal_percentage"
                        value="SAO Percentage"
                        required
                    />

                    <PercentageInput
                        v-model="partner.demo_scheduled_deal_percentage"
                        :maximum="100"
                        @update:model-value="(newValue) => handlePercentageChange(newValue, 'SAO', index)"
                    />

                    <InputError
                        class="mt-2"
                        :message="(form.errors as Record<string, string>)[`partners.${index}.demo_scheduled_deal_percentage`]"
                    />
                </div>

                <div>
                    <InputLabel
                        for="deal_percentage"
                        value="ARR Percentage"
                        required
                    />

                    <PercentageInput
                        v-model="partner.deal_won_deal_percentage"
                        :maximum="100"
                        @update:model-value="(newValue) => handlePercentageChange(newValue, 'ARR', index)"
                    />

                    <InputError
                        class="mt-2"
                        :message="(form.errors as Record<string, string>)[`partners.${index}.deal_won_deal_percentage`]"
                    />
                </div>
            </div>
        </div>

        <SecondaryButton
            @click.prevent="addPartner"
            text="+ Add Partner"
        />
    </SlideOver>
</template>
