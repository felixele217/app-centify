<script setup lang="ts">
import queryParamValue from '@/utils/queryParamValue'
import {
    ClipboardDocumentCheckIcon,
    ExclamationCircleIcon,
    HandThumbDownIcon,
    HandThumbUpIcon,
} from '@heroicons/vue/24/outline'

const tabs = [
    {
        name: 'All',
        href: route('deals.index'),
        current: queryParamValue('scope') === '',
        icon: ClipboardDocumentCheckIcon,
    },
    {
        name: 'Open',
        href: route('deals.index') + '?scope=open',
        current: window.location.href.includes('open'),
        icon: ExclamationCircleIcon,
    },
    {
        name: 'Accepted',
        href: route('deals.index') + '?scope=accepted',
        current: window.location.href.includes('accepted'),
        icon: HandThumbUpIcon,
    },
    {
        name: 'Declined',
        href: route('deals.index') + '?scope=declined',
        current: window.location.href.includes('declined'),
        icon: HandThumbDownIcon,
    },
]
</script>

<template>
    <div>
        <nav
            class="isolate flex w-128 divide-x divide-gray-200 rounded-lg shadow"
            aria-label="Tabs"
        >
            <a
                v-for="(tab, tabIdx) in tabs"
                :key="tab.name"
                :href="tab.href"
                :class="[
                    tab.current ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700',
                    tabIdx === 0 ? 'rounded-l-lg' : '',
                    tabIdx === tabs.length - 1 ? 'rounded-r-lg' : '',
                    'group relative flex min-w-0 flex-1 items-center justify-center gap-2 overflow-hidden bg-white py-2.5 text-center text-sm font-medium hover:bg-gray-50 focus:z-10',
                ]"
                :aria-current="tab.current ? 'page' : undefined"
            >
                <component
                    :is="tab.icon"
                    class="h-5 w-5 shrink-0"
                    aria-hidden="true"
                />

                <span>{{ tab.name }}</span>

                <span
                    aria-hidden="true"
                    :class="[tab.current ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
                />
            </a>
        </nav>
    </div>
</template>
