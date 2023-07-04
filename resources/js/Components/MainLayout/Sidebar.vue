<script setup lang="ts">
import ApplicationLogo from '@/Components/Navigation/ApplicationLogo.vue'
import type NavigationItem from '@/types/NavigationItem'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    navigation: Array<NavigationItem>
}>()
</script>

<template>
    <div class="hidden border-r border-gray-200 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
            <ApplicationLogo class="flex h-16 shrink-0 items-center" />

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
                                            ? 'bg-gray-50 text-primary'
                                            : 'text-gray-800 hover:bg-gray-50 hover:text-primary',
                                        'group flex gap-x-3 rounded-md p-2 py-3.5 font-semibold leading-6',
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
                            class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-800 hover:bg-gray-50 hover:text-primary"
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
    </div>
</template>
