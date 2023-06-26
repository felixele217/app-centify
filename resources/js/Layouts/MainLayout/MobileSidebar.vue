<script setup lang="ts">
import ApplicationLogo from '@/Components/Navigation/ApplicationLogo.vue'
import type NavigationItem from '@/types/NavigationItem'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    navigation: Array<NavigationItem>
}>()
</script>

<template>
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10">
        <div class="flex h-16 shrink-0 items-center">
            <ApplicationLogo />
        </div>
        <nav class="flex flex-1 flex-col">
            <ul
                role="list"
                class="flex flex-1 flex-col gap-y-7"
            >
                <li>
                    <ul
                        role="list"
                        class="-mx-2 space-y-1"
                    >
                        <li
                            v-for="item in props.navigation"
                            :key="item.name"
                        >
                            <a
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-800 text-white'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                    'group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    class="h-6 w-6 shrink-0"
                                    aria-hidden="true"
                                />
                                {{ item.name }}
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="mt-auto">
                    <a
                        :href="route('profile.edit')"
                        class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                    >
                        <Cog6ToothIcon
                            class="h-6 w-6 shrink-0"
                            aria-hidden="true"
                        />
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>
