<script setup lang="ts">
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'

export type SelectOption = {
    title: string
    description: string
    current: boolean
}

const props = defineProps<{
    options: Array<SelectOption>
}>()

const selected = ref<null | {
    title: string
    description: string
    current: boolean
}>(null)

</script>

<template>
    <Listbox
        as="div"
        v-model="selected"
    >
        <ListboxLabel class="sr-only">Change published status</ListboxLabel>

        <div
            class="relative mt-1 flex w-full rounded-md border-0 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
        >
            <ListboxButton
                class="inline-flex grow divide-x divide-gray-300"
            >
                <div class="inline-flex grow items-center gap-x-1.5 px-3 py-2 focus:ring-2 focus:ring-inset">
                    <CheckIcon
                        class="-ml-0.5 h-5 w-5"
                        :class="selected ? 'text-gray-600' : 'text-gray-300'"
                        aria-hidden="true"
                    />
                    <p
                        class="text-sm"
                        :class="selected ? 'text-gray-900' : 'text-gray-300'"
                    >
                        {{ selected ? selected.title : 'Select a Variable..' }}
                    </p>
                </div>

                <div
                    class="hover:bg-i inline-flex items-center rounded-l-none rounded-r-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-gray-50"
                >
                    <span class="sr-only">Change published status</span>

                    <ChevronDownIcon
                        class="h-5 w-5 text-gray-600"
                        aria-hidden="true"
                    />
                </div>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute left-0 top-10 z-10 mt-2 w-72 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <ListboxOption
                        as="template"
                        v-for="option in props.options"
                        :key="option.title"
                        :value="option"
                        v-slot="{ active, selected }"
                    >
                        <li
                            class="cursor-pointer select-none p-4 text-sm"
                            :class="active ? 'bg-indigo-600 text-white' : 'text-gray-900'"
                        >
                            <div class="flex flex-col">
                                <div class="flex justify-between">
                                    <p :class="selected ? 'font-semibold' : 'font-normal'">{{ option.title }}</p>
                                    <span
                                        v-if="selected"
                                        :class="active ? 'text-white' : 'text-indigo-600'"
                                    >
                                        <CheckIcon
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <p :class="[active ? 'text-indigo-200' : 'text-gray-500', 'mt-2']">
                                    {{ option.description }}
                                </p>
                            </div>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
