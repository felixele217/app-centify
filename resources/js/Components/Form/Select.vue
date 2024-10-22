<script setup lang="ts">
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import { computed } from 'vue'

const props = defineProps<{
    options: Array<string>
    modelValue: string
    noOptionsText?: string
}>()

const emit = defineEmits<{
    'update:modelValue': [title: string]
}>()

function handleClear() {
    emit('update:modelValue', '')
    current.value = ''
}

const current = ref<string>(props.modelValue)
</script>

<template>
    <Listbox
        as="div"
        v-model="current"
        @keyup.enter.prevent="$emit('update:modelValue', current)"
    >
        <div class="relative mt-2">
            <ListboxButton
                class="relative flex w-full cursor-pointer items-center justify-between rounded-md bg-white py-2 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm sm:leading-6"
            >
                <div class="flex items-center gap-x-1.5">
                    <CheckIcon
                        v-if="current"
                        class="-ml-0.5 h-4 w-4"
                        :class="current ? 'text-gray-600' : 'text-gray-300'"
                        aria-hidden="true"
                    />

                    <span
                        class="block truncate text-sm"
                        :class="current ? 'text-gray-900' : 'text-gray-300'"
                        >{{ current || 'Select...' }}</span
                    >
                </div>

                <div>
                    <XMarkIcon
                        @click.prevent="handleClear"
                        class="-mr-3 h-5 w-5 text-gray-400 hover:text-gray-600"
                        v-if="current"
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
                    <ListboxOption v-if="props.options.length === 0">
                        <li class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900">
                            {{ props.noOptionsText || 'No Options...' }}
                        </li>
                    </ListboxOption>

                    <ListboxOption
                        as="template"
                        v-else
                        v-for="option in props.options"
                        :key="option"
                        :value="option"
                        @click="$emit('update:modelValue', option)"
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
                            }}</span>

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
