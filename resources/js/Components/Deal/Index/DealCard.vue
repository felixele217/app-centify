<script setup lang="ts">
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Modal from '@/Components/Modal.vue'
import Deal from '@/types/Deal'
import Integration from '@/types/Integration'
import paymentCycle from '@/utils/Date/paymentCycle'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { CheckIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import DealStatus from './DealStatus.vue'

const props = defineProps<{
    deal: Deal
    integrations: Array<Integration>
}>()

function updateDeal() {
    const body = dealIdOfNoteBeingEdited.value
        ? {
              note: noteText.value,
          }
        : dealIdBeingAccepted.value
        ? {
              has_accepted_deal: !!dealIdBeingAccepted.value,
          }
        : {
              rejection_reason: rejectionReason.value,
          }

    router.put(route('deals.update', props.deal.id), body, {
        onSuccess: () => {
            if (dealIdOfNoteBeingEdited.value) {
                notifyEditedNote()
                dealIdOfNoteBeingEdited.value = undefined
                noteText.value = ''
            } else {
                notifyAcceptDecline()
                dealIdBeingAccepted.value = null
                dealIdBeingDeclined.value = null
            }
        },
    })
}

function notifyAcceptDecline() {
    const title = dealIdBeingAccepted.value ? 'Deal accepted!' : 'Deal declined!'
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

const rejectionReason = ref<string>('')
</script>

<template>
    <td class="col-span-3 py-4 pl-6 pr-3">
        <p class="text-gray-900">{{ deal.agent!.name }}</p>
        <p class="mt-1 text-gray-500">{{ deal.agent!.email }}</p>
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
        {{ paymentCycle(deal.add_time) }}
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
                @keyup.enter="updateDeal"
            />

            <CheckIcon
                class="h-7 w-7 rounded-full bg-gray-100 px-1.5 py-1 hover:bg-green-100 hover:text-green-900"
                @click="updateDeal"
            />
        </div>
    </td>

    <td class="px-3">
        <DealStatus
            :deal="deal"
            @accepted="(id: number) => dealIdBeingAccepted = id"
            @declined="(id: number) => dealIdBeingDeclined = id"
        />
    </td>

    <Modal
        @modal-action="updateDeal"
        :isOpen="!!dealIdBeingAccepted"
        @close-modal="dealIdBeingAccepted = null"
        button-text="Accept"
        title="Accept Deal"
        :description="'Are you sure you want to accept this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
    />

    <Modal
        @modal-action="updateDeal"
        is-negative-action
        :isOpen="!!dealIdBeingDeclined"
        @close-modal="dealIdBeingDeclined = null"
        button-text="Decline"
        title="Decline Deal"
        :description="'Are you sure you want to decline this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
    >
        <div class="mt-5">
            <InputLabel
                value="Reason"
                required
            />

            <TextInput v-model="rejectionReason" />

            <InputError
                class="mt-2"
                :message="usePage().props.errors.rejection_reason"
            />
        </div>
    </Modal>
</template>
