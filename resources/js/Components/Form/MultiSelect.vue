<script setup lang="ts">
import DeleteIcon from '@/Components/Icon/DeleteIcon.vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps<{
    options: Array<{
        id: number
        name: string
    }>
    selectedIds: Array<number>
}>()

function isSelected(optionId: number) {
    return props.selectedIds.includes(optionId)
}

const selectedOptions = () => props.options.filter((option) => props.selectedIds.includes(option.id))
</script>

<template>
    <Listbox
        as="div"
        multiple
    >
        <div class="relative mt-2">
            <ListboxButton
                class="relative w-full cursor-pointer rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6"
            >
                <span class="block truncate text-gray-300">Assign to agents...</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                    />
                </span>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-2/3 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        as="template"
                        v-for="option in props.options"
                        :key="option.id"
                        :value="option"
                        v-slot="{ active }"
                    >
                        <li
                            @click="$emit('agent-clicked', option.id)"
                            :class="[
                                active ? 'bg-primary text-white' : 'text-gray-900',
                                'relative cursor-pointer select-none py-2 pl-8 pr-4',
                            ]"
                        >
                            <span
                                :class="[isSelected(option.id) ? 'font-semibold' : 'font-normal', 'block truncate']"
                                >{{ option.name }}</span
                            >

                            <span
                                v-if="isSelected(option.id)"
                                :class="[
                                    active ? 'text-white' : 'text-primary',
                                    'absolute inset-y-0 left-0 flex items-center pl-1.5',
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

            <div class="mt-2 space-y-1">
                <div
                    class="flex items-center justify-end gap-2 text-sm text-gray-600"
                    v-for="option in selectedOptions()"
                >
                    <p>{{ option.name }}</p>
                    <DeleteIcon />
                </div>
            </div>
        </div>
    </Listbox>
</template>
