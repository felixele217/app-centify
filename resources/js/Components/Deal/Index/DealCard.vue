<script setup lang="ts">
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Toggle from '@/Components/Form/Toggle.vue'
import Modal from '@/Components/Modal.vue'
import Deal from '@/types/Deal'
import Integration from '@/types/Integration'
import attributionPeriod from '@/utils/Date/attributionPeriod'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { CheckIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import DealStatus from './DealStatus.vue'
import SplitArrowsIcon from '@/Components/Icon/SplitArrowsIcon.vue'

const vFocus = {
    mounted: (el: HTMLInputElement) => el.focus(),
}

const props = defineProps<{
    deal: Deal
    integrations: Array<Integration>
}>()

const acceptDeal = () => updateDeal({ has_accepted_deal: true })
const updateDealNote = () => updateDeal({ note: noteText.value })

const declineDeal = () => {
    router.post(
        route('deals.rejections.store', props.deal.id),
        {
            rejection_reason: rejectionForm.reason,
            is_permanent: rejectionForm.is_permanent,
        },
        {
            onSuccess: () => {
                notifyAcceptDecline()
                dealIdBeingDeclined.value = null
                rejectionForm.reset()
            },
        }
    )
}

function updateDeal(body: any) {
    router.put(route('deals.update', props.deal.id), body, {
        onSuccess: () => {
            if (dealIdOfNoteBeingEdited.value) {
                notifyEditedNote()
                dealIdOfNoteBeingEdited.value = undefined
            } else {
                notifyAcceptDecline()
                dealIdBeingAccepted.value = null
            }
        },
    })
}

function notifyAcceptDecline() {
    const title = dealIdBeingAccepted.value ? 'Deal accepted!' : 'Deal rejected!'
    const description = dealIdBeingAccepted.value
        ? "It now counts towards this agent's commission metrics."
        : "This deal will not affect this agent's commission metrics."

    notify(title, description)
}

function notifyEditedNote() {
    const title = 'Deal Note edited!'
    const description = 'This deal now has the note you just added.'

    notify(title, description)
}

const pipedriveSubdomain = computed(() => props.integrations[0].subdomain)

const dealIdBeingAccepted = ref<number | null>()
const dealIdBeingDeclined = ref<number | null>()
const dealIdOfNoteBeingEdited = ref<number>()
const noteText = ref<string>(props.deal.note ?? '')

const rejectionForm = useForm({
    reason: '',
    is_permanent: false,
})
</script>

<template>
    <td class="col-span-3 flex items-center justify-between py-4 pl-6 pr-3">
        <div>
            <p class="text-gray-900">{{ deal.agent!.name }}</p>
            <p class="mt-1 text-gray-500">{{ deal.agent!.email }}</p>
        </div>
        <SplitArrowsIcon class="mr-3 cursor-pointer" />
    </td>

    <td class="col-span-2 px-3 py-4 text-gray-500">
        <a
            class="link"
            :href="`https://${pipedriveSubdomain}.pipedrive.com/deal/${deal.integration_deal_id}`"
            target="_blank"
        >
            {{ deal.title }}
        </a>
        <p>{{ euroDisplay(deal.value) }}</p>
    </td>

    <td class="col-span-2 px-3 py-4 text-gray-500">
        {{ attributionPeriod(deal.add_time) }}
    </td>

    <td class="col-span-4 px-3 py-4 text-gray-500">
        <div
            v-if="!dealIdOfNoteBeingEdited"
            class="flex cursor-pointer items-center gap-1.5 hover:text-black"
            @click="dealIdOfNoteBeingEdited = deal.id"
        >
            <p class="truncate">{{ deal.note ?? 'Add Note' }}</p>

            <PencilSquareIcon class="h-4 w-4" />
        </div>

        <div
            v-else
            class="flex cursor-pointer items-center gap-1.5"
        >
            <TextInput
                no-top-margin
                v-model="noteText"
                @keyup.enter="updateDealNote"
                v-focus
                @blur="dealIdOfNoteBeingEdited = undefined"
                @keyup.escape="dealIdOfNoteBeingEdited = undefined"
            />

            <CheckIcon
                class="h-7 w-7 rounded-full bg-gray-100 px-1.5 py-1 hover:bg-green-100 hover:text-green-900"
                @click="updateDealNote"
            />
        </div>
    </td>

    <td class="px-3">
        <DealStatus
            :deal="deal"
            @accepted="(id: number) => dealIdBeingAccepted = id"
            @rejected="(id: number) => dealIdBeingDeclined = id"
        />
    </td>

    <Modal
        @modal-action="acceptDeal"
        :isOpen="!!dealIdBeingAccepted"
        @close-modal="dealIdBeingAccepted = null"
        button-text="Accept"
        title="Accept Deal"
        :description="'Are you sure you want to accept this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
    />

    <Modal
        @modal-action="declineDeal"
        is-negative-action
        :isOpen="!!dealIdBeingDeclined"
        @close-modal="dealIdBeingDeclined = null"
        button-text="Reject"
        title="Reject Deal"
        :description="'Are you sure you want to reject this deal? \nThe deal will reappear the following month, unless you reject it permanently.'"
    >
        <div class="mt-8">
            <InputLabel
                value="Reason"
                required
            />
            <TextInput
                v-model="rejectionForm.reason"
                autofocus
            />
            <InputError
                class="mt-2"
                :message="usePage().props.errors.rejection_reason"
            />
        </div>

        <Toggle
            color="red"
            class="mt-5"
            title="Reject permanently"
            description="After rejecting a deal permanently, it ceases to reappear each month."
            v-model="rejectionForm.is_permanent"
        />
    </Modal>
</template>
