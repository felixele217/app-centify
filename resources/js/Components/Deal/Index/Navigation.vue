<script setup lang="ts">
const tabs = [
    { name: 'All', href: route('deals.index'), current: false },
    { name: 'Open', href: route('deals.index') + '?scope=open', current: true },
    { name: 'Accepted', href: route('deals.index') + '?scope=accepted', current: false },
    { name: 'Declined', href: route('deals.index') + '?scope=declined', current: false },
]
</script>

<template>
    <div>
        <nav
            class="isolate flex w-144 divide-x divide-gray-200 rounded-lg shadow"
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
                    'group relative min-w-0 flex-1 overflow-hidden bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10',
                ]"
                :aria-current="tab.current ? 'page' : undefined"
            >
                <span>{{ tab.name }}</span>
                <span
                    aria-hidden="true"
                    :class="[tab.current ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
                />
            </a>
        </nav>
    </div>
</template>
