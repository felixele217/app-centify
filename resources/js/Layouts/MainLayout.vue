<script setup lang="ts">
import Sidebar from '@/Components/MainLayout/Sidebar.vue'
import Notification from '@/Components/Notification.vue'
import {
    ChartPieIcon,
    ClipboardDocumentCheckIcon,
    FolderIcon,
    HomeIcon,
    PuzzlePieceIcon,
} from '@heroicons/vue/24/outline'

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    {
        name: 'Deals',
        href: route('deals.index') + '?scope=open',
        icon: ClipboardDocumentCheckIcon,
        current: route().current('deals.index'),
    },
    { name: 'Plans', href: route('plans.index'), icon: ChartPieIcon, current: route().current('plans.index') },
    { name: 'Teams & Users', href: route('agents.index'), icon: FolderIcon, current: route().current('agents.index') },
    {
        name: 'Integrations',
        href: route('integrations.index'),
        icon: PuzzlePieceIcon,
        current: route().current('integrations.index'),
    },
]
</script>

<template>
    <div>
        <Sidebar :navigation="navigation" />

        <div class="flex flex-col lg:pl-72">
            <main class="grow p-5 sm:px-10 lg:p-10">
                <slot />
            </main>
        </div>
    </div>

    <Notification />
</template>
