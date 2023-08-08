<script setup lang="ts">
const props = defineProps<{
    name: string
    href?: string
    current: boolean
    icon: any
}>()
</script>

<template>
    <a
        :href="props.href"
        :class="[
            props.current ? 'bg-gray-50 text-primary' : 'text-gray-800 hover:bg-gray-50 hover:text-primary',
            'group flex gap-x-3 rounded-md p-2 py-3.5 font-semibold leading-6',
        ]"
    >
        <component
            :is="props.icon"
            class="h-6 w-6 shrink-0"
            aria-hidden="true"
        />
        {{ props.name }}
    </a>
</template>
