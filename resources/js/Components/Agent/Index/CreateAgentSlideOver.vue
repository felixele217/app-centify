<script setup lang="ts">
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    base_salary: null,
    on_target_earning: null,
})

const emit = defineEmits(['close-slide-over'])

defineProps<{
    isOpen: boolean
}>()

function submit() {
    form.post(route('agents.store'), {
        onSuccess: () => emit('close-slide-over'),
    })
}
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

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
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
                                    @submit.prevent="submit"
                                >
                                    <div class="h-0 flex-1 overflow-y-auto">
                                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-semibold leading-6 text-white"
                                                    >Create Agent</DialogTitle
                                                >
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
                                                <p class="text-sm text-indigo-300">
                                                    Create a new Agent for your Organization.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 flex-col justify-between">
                                            <div class="divide-y divide-gray-200 px-4 sm:px-6">
                                                <div class="space-y-6 pb-5 pt-6">
                                                    <div class="leading-6">
                                                        <InputLabel
                                                            for="name"
                                                            value="Name"
                                                            required
                                                        />

                                                        <TextInput
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.name"
                                                            :border="true"
                                                            name="name"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.name"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="email"
                                                            value="Email"
                                                            required
                                                        />

                                                        <TextInput
                                                            id="email"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            v-model="form.email"
                                                            name="email"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.email"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="base_salary"
                                                            value="Base Salary"
                                                        />

                                                        <TextInput
                                                            name="base_salary"
                                                            id="base_salary"
                                                            type="number"
                                                            class="mt-1 block w-full"
                                                            v-model="form.base_salary"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.base_salary"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            for="on_target_earning"
                                                            value="On Target Earning (OTE)"
                                                        />

                                                        <TextInput
                                                            id="on_target_earning"
                                                            type="number"
                                                            class="mt-1 block w-full"
                                                            v-model="form.on_target_earning"
                                                        />

                                                        <InputError
                                                            class="mt-2"
                                                            :message="form.errors.on_target_earning"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-shrink-0 justify-end px-4 py-4">
                                        <button
                                            type="button"
                                            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                            @click="$emit('close-slide-over')"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                        >
                                            Create
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
