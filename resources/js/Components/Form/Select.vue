<script setup lang="ts">
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    options: Array<string>
    selectedOption: string
    noOptionsText?: string
}>()

defineEmits<{
    'option-selected': [option: string]
}>()
</script>

<template>
    <Listbox as="div">
        <div class="relative mt-2">
            <ListboxButton
                class="relative flex w-full cursor-pointer items-center justify-between rounded-md bg-white py-2 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
            >
                <div class="flex items-center gap-x-1.5">
                    <CheckIcon
                        v-if="props.selectedOption"
                        class="-ml-0.5 h-4 w-4"
                        :class="props.selectedOption ? 'text-gray-600' : 'text-gray-300'"
                        aria-hidden="true"
                    />
                    <span
                        class="block truncate text-sm"
                        :class="props.selectedOption ? 'text-gray-900' : 'text-gray-300'"
                        >{{ props.selectedOption ? props.selectedOption : 'Select...' }}</span
                    >
                </div>

                <div>
                    <XMarkIcon
                        @click.prevent="$emit('option-selected', '')"
                        class="-mr-3 h-5 w-5 text-gray-400 hover:text-gray-600"
                        v-if="props.selectedOption"
                    />

                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                        />
                    </span>
                </div>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        v-if="props.options.length === 0"
                    >
                        <li
                            class="text-gray-900 relative cursor-pointer select-none py-2 pl-3 pr-9"
                        >
                          {{ props.noOptionsText || 'No Options...' }}
                        </li>
                    </ListboxOption>

                    <ListboxOption
                        as="template"
                        v-else
                        v-for="option in props.options"
                        :key="option"
                        :value="option"
                        @click="$emit('option-selected', option)"
                        v-slot="{ active, selected }"
                    >
                        <li
                            :class="[
                                active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                'relative cursor-pointer select-none py-2 pl-3 pr-9',
                            ]"
                        >
                            <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                option
                            }}hallo</span>

                            <span
                                v-if="selected"
                                :class="[
                                    active ? 'text-white' : 'text-indigo-600',
                                    'absolute inset-y-0 right-0 flex items-center pr-4',
                                ]"
                            >
                                <CheckIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
