<script setup lang="ts">
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Toggle from '@/Components/Form/Toggle.vue'
import SplitArrowsIcon from '@/Components/Icon/SplitArrowsIcon.vue'
import Modal from '@/Components/Modal.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Deal from '@/types/Deal'
import { TriggerEnum } from '@/types/Enum/TriggerEnum'
import Integration from '@/types/Integration'
import attributionPeriod from '@/utils/Date/attributionPeriod'
import insertNewLines from '@/utils/StringManipulation/insertNewLines'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { CheckIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import DealStatus from './DealStatus.vue'
import SplitDealSlideOver from './SplitDealSlideOver.vue'

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
    isUpdatingDeal.value = true

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
        onFinish: () => {
            isUpdatingDeal.value = false
            noteText.value = props.deal.note ?? ''
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
const isUpdatingDeal = ref<boolean>(false)
const noteText = ref<string>(props.deal.note ?? '')

const rejectionForm = useForm({
    reason: '',
    is_permanent: false,
})

const isSplittingDeal = ref<boolean>(false)

const handleBlur = () =>
    setTimeout(() => {
        if (!isUpdatingDeal.value) {
            dealIdOfNoteBeingEdited.value = undefined
        }
    }, 100)

const agentThatTriggeredDeal = computed(() => {
    return props.deal.a_e ?? props.deal.s_d_r!
})

const shareholdersCount = computed(
    () =>
        [
            ...new Set([
                ...Object.values(props.deal.demo_scheduled_shareholders!).map((agent) => agent.id),
                ...Object.values(props.deal.deal_won_shareholders!).map((agent) => agent.id),
            ]),
        ].length
)

function dealPercentages(trigger: TriggerEnum) {
    const agentsWithTrigger = props.deal.agents!.filter((agent) => agent.pivot.triggered_by === trigger)

    if (agentsWithTrigger.length < 2) {
        return ''
    }

    return (
        (trigger === 'Demo scheduled' ? 'SAO Splits:\n' : 'ARR Splits:\n') +
        agentsWithTrigger.map((agent) => '\t' + agent.name + ': ' + agent.pivot.deal_percentage + '%').join('\n') +
        '\n\n'
    )
}
</script>

<template>
    <td class="col-span-3 flex items-center justify-between py-4 pl-6 pr-3">
        <div>
            <p class="text-gray-900">{{ agentThatTriggeredDeal.name }}</p>

            <Tooltip
                v-if="shareholdersCount"
                :text="`${dealPercentages('Demo scheduled')}${dealPercentages('Deal won')}`"
                placement="bottom"
                class="whitespace-pre-wrap"
            >
                <p class="mt-1 text-gray-500">+{{ shareholdersCount }} more due to split</p>
            </Tooltip>
            <p
                v-else
                class="mt-1 text-gray-500"
            >
                {{ agentThatTriggeredDeal.email }}
            </p>
        </div>

        <SplitArrowsIcon
            class="mr-3 cursor-pointer"
            @click="isSplittingDeal = true"
        />
    </td>

    <td class="col-span-2 px-3 py-4 text-gray-500">
        <a
            class="link"
            :href="`https://${pipedriveSubdomain}.pipedrive.com/deal/${deal.integration_deal_id}`"
            target="_blank"
        >
            {{ props.deal.title }}
        </a>
        <p>{{ euroDisplay(props.deal.value) }}</p>
    </td>

    <td class="col-span-2 mr-5 px-3 py-4 text-sm text-gray-500">
        <div class="flex gap-1">
            <p>SAO in</p>
            <span class="font-semibold">
                {{ attributionPeriod(props.deal.add_time) }}
            </span>
        </div>
        <div
            class="flex gap-1"
            v-if="props.deal.won_time"
        >
            <p>won in</p>
            <span class="font-semibold">
                {{ attributionPeriod(props.deal.won_time) }}
            </span>
        </div>
    </td>

    <td class="col-span-4 mr-5 px-3 py-4 text-gray-500">
        <div
            v-if="!dealIdOfNoteBeingEdited"
            class="flex cursor-pointer items-center gap-1.5 break-words hover:text-black"
            @click="dealIdOfNoteBeingEdited = props.deal.id"
        >
            <Tooltip
                :text="props.deal.note ? insertNewLines(props.deal.note) : ''"
                placement="top"
                class="max-w-lg whitespace-pre-wrap"
            >
                <p class="line-clamp-2">{{ props.deal.note ?? 'Add Note' }}</p>
            </Tooltip>

            <div>
                <PencilSquareIcon class="h-4 w-4" />
            </div>
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
                @blur="handleBlur"
                @keyup.escape="dealIdOfNoteBeingEdited = undefined"
            />

            <CheckIcon
                class="mr-2 h-7 w-7 rounded-full bg-gray-100 px-1.5 py-1 hover:bg-primary-50 hover:text-primary-500"
                @click="updateDealNote"
            />
        </div>
    </td>

    <td class="mr-10 px-3 2xl:mr-0">
        <DealStatus
            :deal="deal"
            @accepted="(id: number) => dealIdBeingAccepted = id"
            @rejected="(id: number) => dealIdBeingDeclined = id"
        />
    </td>

    <SplitDealSlideOver
        @close-slide-over="isSplittingDeal = false"
        :deal="props.deal"
        :is-open="isSplittingDeal"
        :agent-that-triggered-deal="agentThatTriggeredDeal"
    />

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
