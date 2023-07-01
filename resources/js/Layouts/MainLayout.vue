<script setup lang="ts">
import ProfileDropdown from '@/Components/MainLayout/ProfileDropdown.vue'
import SearchBar from '@/Components/MainLayout/SearchBar.vue'
import Sidebar from '@/Components/MainLayout/Sidebar.vue'
import { BellIcon, ChartPieIcon, FolderIcon, HomeIcon, PuzzlePieceIcon } from '@heroicons/vue/24/outline'

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    { name: 'Teams & Users', href: route('agents.index'), icon: FolderIcon, current: route().current('agents.index') },
    { name: 'Plans', href: route('plans.index'), icon: ChartPieIcon, current: route().current('plans') },
    {
        name: 'Integrations',
        href: route('integrations'),
        icon: PuzzlePieceIcon,
        current: route().current('integrations'),
    },
]
</script>

<template>
    <div>
        <Sidebar :navigation="navigation" />

        <div class="flex h-screen flex-col lg:pl-72">
            <div
                class="flex self-stretch items-center gap-x-4 border-b border-gray-200 bg-white px-8 py-3 shadow-sm lg:gap-x-6"
            >
                <SearchBar />

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <button
                        type="button"
                        class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">View notifications</span>
                        <BellIcon
                            class="h-6 w-6"
                            aria-hidden="true"
                        />
                    </button>

                    <div
                        class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10"
                        aria-hidden="true"
                    />

                    <ProfileDropdown />
                </div>
            </div>

            <main class="grow p-5 sm:px-6 lg:px-10">
                <slot />
            </main>
        </div>
    </div>
</template>
