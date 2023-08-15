<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import notify from '@/utils/notify'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useForm, usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import SlideOver from '@/Components/SlideOver.vue'
import Deal from '@/types/Deal'
import PercentageInput from '@/Components/Form/PercentageInput.vue'
import Select from '@/Components/Form/Select.vue'
import { computed } from 'vue'
import SecondaryButton from '@/Components/Buttons/SecondaryButton.vue'
import sum from '@/utils/sum'

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
    partners: props.deal.splits!.length
        ? loadExistingAgentsFromSplits()
        : [newPartner],
})

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
    console.log('before')
    console.log(form.partners)
    form.partners[index].name = name
    form.partners[index].id = agentNamesToIds.value[name]
    console.log('after')
    console.log(form.partners)
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
        title="Split Deal"
        button-text="Split"
        description="You can split deals to accomodate the profit of more than one agent for a deal."
    >
        <div class="px-6">
            <div class="my-5 text-gray-700">
                <p>
                    <span class="font-semibold">
                        {{ props.deal.agent!.name }}
                    </span>
                    retains
                    <span class="font-semibold">
                        {{ 100 - sum(form.partners.map((partner) => partner.shared_percentage || 0)) }}%
                    </span>
                    of the deal.
                </p>

                <p v-for="partner in form.partners" class="text-gray-700">
                    <span class="font-semibold">
                        {{ partner.name }}
                    </span>
                    retains
                    <span class="font-semibold">
                        {{ partner.shared_percentage }}%
                    </span>
                    of the deal.
                </p>
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

                        <XMarkIcon
                            class="h-6 w-6 text-gray-700 hover:text-black cursor-pointer rounded-full p-1 hover:bg-gray-100"
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

            <p
                class="cursor-pointer text-sm text-gray-700 hover:underline hover:underline-offset-2"
                @click="addPartner"
            >
                Add Partner +
            </p>
        </div>
    </SlideOver>
</template>
