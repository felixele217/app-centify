<script setup lang="ts">
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    text: string
    color: 'green' | 'red' | 'indigo' | 'yellow' | 'purple'
    withDelete?: boolean
}>()

defineEmits<{
    delete: []
}>()

function color() {
    switch (props.color) {
        case 'red':
            return 'text-red-700 bg-red-100'
        case 'green':
            return 'text-green-700 bg-green-100'
        case 'indigo':
            return 'text-indigo-700 bg-indigo-100'
        case 'yellow':
            return 'text-yellow-700 bg-yellow-100'
        case 'purple':
            return 'text-purple-700 bg-purple-100'
    }
}
</script>

<template>
    <div
        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
        :class="color()"
    >
        <p>
            {{ props.text }}
        </p>

        <XMarkIcon
            v-if="props.withDelete"
            :class="color()"
            class="h-6 w-6 cursor-pointer rounded-full p-1 text-gray-400 hover:bg-red-100 hover:text-red-500"
            aria-hidden="true"
            @click="$emit('delete')"
        />
    </div>
</template>
