<script setup lang="ts">
const props = defineProps<{
    text: string
}>()
</script>

<template>
    <div
        class="block w-full cursor-pointer px-4 py-2 text-left text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <p>{{ props.text }}</p>
    </div>
</template>
