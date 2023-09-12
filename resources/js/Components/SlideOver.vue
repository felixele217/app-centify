<script setup lang="ts">
import FormButtons from '@/Components/Form/FormButtons.vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = withDefaults(
    defineProps<{
        isOpen: boolean
        title: string
        description: string
        buttonText?: string
        width?: string
    }>(),
    {
        width: 'w-112',
    }
)

defineEmits<{
    'close-slide-over': []
    submit: []
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
                            <DialogPanel
                                class="pointer-events-auto"
                                :class="props.width"
                            >
                                <form
                                    class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl"
                                    @submit.prevent="$emit('submit')"
                                >
                                    <div class="flex h-0 flex-1 flex-col overflow-y-auto">
                                        <div class="bg-primary-500 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-white">{{
                                                    props.title
                                                }}</DialogTitle>
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button
                                                        type="button"
                                                        class="rounded-md text-white hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
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
                                                <p class="text-sm text-white">{{ props.description }}</p>
                                            </div>
                                        </div>
                                        <div class="grow overflow-y-scroll">
                                            <div class="mt-4 space-y-6 px-6 pb-5 pt-3">
                                                <slot />
                                            </div>
                                        </div>
                                    </div>

                                    <FormButtons
                                        v-if="props.buttonText"
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
