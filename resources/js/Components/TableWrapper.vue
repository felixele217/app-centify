<script setup lang="ts">
const props = defineProps<{
    noItemsText?: string
}>()
</script>

<template>
    <div class="mt-8 flow-root">
        <div
            class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"
            v-if="!props.noItemsText"
        >
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="ring-1 ring-black ring-opacity-5 drop-shadow-md sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <slot name="header" />
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <slot name="body" />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p
            v-else
            class="text-base leading-6 text-gray-900"
        >
            {{ props.noItemsText }}
        </p>
    </div>
</template>
