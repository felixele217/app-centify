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

const emit = defineEmits<{
    'close-slide-over': []
}>()

const props = defineProps<{
    isOpen: boolean
    deal: Deal
}>()

const agents = computed(() => usePage().props.agents as Record<string, number>)

const form = useForm({
    partners: [
        {
            name: '',
            id: null as number | null,
            shared_percentage: 0,
        },
    ],
})

function closeSlideOver() {
    form.reset()

    emit('close-slide-over')
}

function submit() {
    form.post(route('deals.splits.store', props.deal.id), {
        onSuccess: () => {
            closeSlideOver()

            notify('Deal splitted', 'This deal will now be splitted between the specified agents.')
        },
    })
}

function handlePartnerSelection(name: string): void {
    form.partners[0].name = name
    form.partners[0].id = agents.value[name]
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
        <div class="space-y-6 px-6 pb-5 pt-3">
            <p class="mt-3 text-gray-700">
                <span class="font-semibold">
                    {{ props.deal.agent!.name }}
                </span>
                retains
                <span class="font-semibold"> {{ 100 - (form.partners[0].shared_percentage || 0) }}% </span>
                of the deal.
            </p>
            <div>
                <InputLabel
                    for="partner"
                    value="Partner"
                    required
                />
                <Select
                    :options="Object.keys(agents)"
                    :selected-option="form.partners[0].name"
                    @option-selected="handlePartnerSelection"
                />
                <InputError
                    class="mt-2"
                    :message="(form.errors as Record<string, string>)['partners.0.id']"
                />
            </div>
            <div>
                <InputLabel
                    for="shared_percentage"
                    value="Shared Percentage"
                    required
                />

                <PercentageInput v-model="form.partners[0].shared_percentage" :maximum="100" />

                <InputError
                    class="mt-2"
                    :message="(form.errors as Record<string, string>)['partners.0.shared_percentage']"
                />
            </div>
        </div>
    </SlideOver>
</template>
