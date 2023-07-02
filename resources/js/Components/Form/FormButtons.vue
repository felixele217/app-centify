<script setup lang="ts">
</script>

<template>
    <div class="flex flex-shrink-0 justify-end gap-3 py-4">
        <button
            type="button"
            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            @click="$emit('cancel-button-clicked')"
        >
            Cancel
        </button>
        <button
            class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            @click="$emit('create-button-clicked')"
        >
            Create
        </button>
    </div>
</template>
