<script setup lang="ts">
import { TimeScopeEnumCases } from '@/EnumCases/TimeScopeEnum'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
    reloadUrl: string
}>()

const sortOptions = TimeScopeEnumCases!.map((timeScope) => {
    return {
        name: timeScope,
        href: props.reloadUrl + '?time_scope=' + timeScope,
    }
})

const currentTimeScope = computed(() => queryParamValue('time_scope') || 'monthly')
const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <Menu
        as="div"
        class="relative inline-block text-left"
    >
        <div>
            <MenuButton class="group">
                <div class="inline-flex justify-center text-sm font-medium text-gray-500 group-hover:text-gray-900">
                    {{ currentTimeScope }}
                    <ChevronDownIcon
                        class="-mr-1 ml-1 h-5 w-5 flex-shrink-0"
                        aria-hidden="true"
                    />
                </div>

                <p class="mt-0.5 text-left font-semibold text-gray-700 group-hover:text-gray-900">
                    {{ currentScope(timeScopeFromQuery) }}
                </p>
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
                <div>
                    <MenuItem
                        v-for="(option, optionId) in sortOptions"
                        :key="option.name"
                        v-slot="{ active }"
                    >
                        <a
                            @click="currentTimeScope = option.name"
                            :href="option.href"
                            :class="[
                                active ? 'bg-gray-100' : '',
                                'block px-4 py-2 text-sm font-medium text-gray-900',
                                optionId === 0 ? 'rounded-t-md' : '',
                                optionId === sortOptions.length - 1 ? 'rounded-b-md' : '',
                            ]"
                            >{{ option.name }}</a
                        >
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
