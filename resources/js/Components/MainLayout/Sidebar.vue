<script setup lang="ts">
import ApplicationLogo from '@/Components/Navigation/ApplicationLogo.vue'
import type NavigationItem from '@/types/NavigationItem'
import SidebarLink from './SidebarLink.vue'
import UserMenu from './UserMenu.vue'

const props = defineProps<{
    navigation: Array<NavigationItem>
}>()
</script>

<template>
    <div class="hidden border-r border-gray-200 lg:fixed lg:inset-y-0 lg:z-0 lg:flex lg:w-72 lg:flex-col">
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
                                <SidebarLink v-bind="item" />
                            </li>
                        </ul>
                    </li>

                    <UserMenu
                        class="-mx-2 cursor-pointer"
                        :navigation="props.navigation"
                    />
                </ul>
            </nav>
        </div>
    </div>
</template>
