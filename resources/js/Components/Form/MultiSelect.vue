<script setup lang="ts">
import SelectOption from '@/types/SelectOption'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import DeleteIcon from '../Icon/DeleteIcon.vue'
import { ref } from 'vue'

const emit = defineEmits<{
    'option-clicked': [id: number]
}>()

const props = defineProps<{
    options: Array<SelectOption>
    selectedIds: Array<number>
}>()

function isSelected(optionId: number) {
    return props.selectedIds.includes(optionId)
}

const selectedOptions = () => props.options.filter((option) => props.selectedIds.includes(option.id))

function handleEnter() {
    const activeElement = document.activeElement as HTMLUListElement
    const activeDescendantId = activeElement.getAttribute('aria-activeDescendant') as string

    const selectedTitle = document.getElementById(activeDescendantId)?.firstChild?.textContent

    if (selectedTitle) {
        const selectedId = props.options.filter((option) => option.name === selectedTitle)[0].id
        emit('option-clicked', selectedId)
    }
}
</script>

<template>
    <Listbox
        as="div"
        @keyup.enter.prevent="handleEnter"
        multiple
    >
        <div class="relative mt-2">
            <ListboxButton
                class="ring-focus-inset relative w-full cursor-pointer rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6"
            >
                <span
                    v-if="!props.selectedIds.length"
                    class="block truncate text-gray-300"
                    >Assign to agents...</span
                >

                <div
                    class="flex flex-wrap gap-2"
                    v-else
                >
                    <div
                        v-for="option in selectedOptions()"
                        class="flex items-center gap-1 rounded-md bg-indigo-600 px-2 text-indigo-50"
                    >
                        <p>
                            {{ option.name }}
                        </p>

                        <DeleteIcon
                            class="h-3.5 w-3.5 text-indigo-50 hover:text-gray-300"
                            version="mark"
                            @click.stop="$emit('option-clicked', option.id)"
                        />
                    </div>
                </div>

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
                    class="absolute z-10 mt-1 max-h-60 w-2/3 overflow-auto rounded-md bg-white text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        as="template"
                        v-for="option in props.options"
                        :key="option.id"
                        :value="option"
                        v-slot="{ active }"
                    >
                        <li
                            @click="$emit('option-clicked', option.id)"
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
        </div>
    </Listbox>
</template>
