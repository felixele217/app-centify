<script setup lang="ts">
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { computed, ref } from 'vue'

export type SelectOptionWithDescription = {
    title: string
    description?: string
    current: boolean
}

const props = defineProps<{
    options: Array<SelectOptionWithDescription>
    defaultTitle?: string
    disabled?: boolean
    modelValue: string
}>()

const emit = defineEmits<{
    'update:modelValue': [title: string]
}>()

function clearSelect() {
    if (props.disabled) {
        return
    }

    current.value.current = false
    emit('update:modelValue', '')
}

const selected = computed(() => props.options.filter((option) => option.title === props.modelValue)[0])

const current = ref<SelectOptionWithDescription>(props.options.filter((option) => option.title === props.modelValue)[0])
</script>

<template>
    <Listbox
        as="div"
        v-model="current"
        @keyup.enter.prevent="$emit('update:modelValue', current.title)"
    >
        <ListboxLabel class="sr-only">Change published status</ListboxLabel>

        <div
            class="ring-focus-inset relative mt-2 flex w-full rounded-md border-0 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
        >
            <ListboxButton class="ring-focus-inset inline-flex grow rounded-md">
                <div class="inline-flex grow items-center gap-x-1.5 px-3 py-2">
                    <CheckIcon
                        v-if="selected"
                        class="-ml-0.5 h-4 w-4"
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
                    class="hover:bg-i inline-flex items-center rounded-l-none rounded-r-md p-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-50"
                >
                    <span class="sr-only">Change published status</span>

                    <XMarkIcon
                        @click.prevent="clearSelect"
                        class="h-5 w-5 text-gray-400 hover:text-gray-600"
                        v-if="!props.disabled && selected?.title"
                    />

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
                    :class="props.disabled ? 'invisible' : ''"
                    class="absolute left-0 top-10 z-10 mt-2 w-72 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <ListboxOption
                        @click.prevent="$emit('update:modelValue', option.title)"
                        as="template"
                        v-for="option in props.options"
                        :key="option.title"
                        :value="option"
                        v-slot="{ active, selected }"
                    >
                        <li
                            class="cursor-pointer select-none p-4 text-sm"
                            :class="active ? 'bg-primary-500 text-white' : 'text-gray-900'"
                        >
                            <div class="flex flex-col">
                                <div class="flex justify-between">
                                    <p :class="selected ? 'font-semibold' : 'font-normal'">{{ option.title }}</p>
                                    <span
                                        v-if="selected"
                                        :class="active ? 'text-white' : 'text-primary-500'"
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
