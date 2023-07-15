<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
    href: string
    text?: string
}>()
</script>

<template>
    <Link
        :href="props.href"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <p v-if="props.text">{{ props.text }}</p>
        <slot else />
    </Link>
</template>
