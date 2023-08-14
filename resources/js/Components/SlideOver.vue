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
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

defineEmits<{
    'close-slide-over': []
    submit: []
}>()

const props = defineProps<{
    isOpen: boolean
    title: string
    description: string
    buttonText: string
}>()
</script>

<template>
    <TransitionRoot
        as="template"
        :show="isOpen"
    >
        <Dialog
            as="div"
            class="relative z-10"
            @close="$emit('close-slide-over')"
        >
            <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-auto">
                <div class="absolute inset-0 overflow-auto">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <form
                                    class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl"
                                    @submit.prevent="$emit('submit')"
                                >
                                    <div class="h-0 flex-1 overflow-y-auto">
                                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-white">{{
                                                    props.title
                                                }}</DialogTitle>
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button
                                                        type="button"
                                                        class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                                        @click="$emit('close-slide-over')"
                                                    >
                                                        <span class="sr-only">Close panel</span>
                                                        <XMarkIcon
                                                            class="h-6 w-6"
                                                            aria-hidden="true"
                                                        />
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-1">
                                                <p class="text-sm text-indigo-300">{{ props.description }}</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 flex-col justify-between">
                                            <div class="divide-y divide-gray-200 overflow-y-scroll px-6">
                                                <div class="space-y-6 pb-5 pt-6">
                                                    <slot />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <FormButtons
                                        :positiveButtonText="props.buttonText"
                                        class="pr-4"
                                        @cancel-button-clicked="$emit('close-slide-over')"
                                    />
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
