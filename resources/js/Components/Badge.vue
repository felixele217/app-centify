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
            return 'text-red-700 bg-red-100 ring-red-600/20'
        case 'green':
            return 'text-green-700 bg-green-100 ring-green-600/20'
        case 'indigo':
            return 'text-indigo-700 bg-indigo-100 ring-indigo-600/20'
        case 'yellow':
            return 'text-yellow-700 bg-yellow-100 ring-yellow-600/20'
        case 'purple':
            return 'text-purple-700 bg-purple-100 ring-purple-600/20'
    }
}

function xIconHoverColor() {
    switch (props.color) {
        case 'red':
            return 'hover:bg-red-600/20'
        case 'green':
            return 'hover:bg-green-600/20'
        case 'indigo':
            return 'hover:bg-indigo-600/20'
        case 'yellow':
            return 'hover:bg-yellow-600/20'
        case 'purple':
            return 'hover:bg-purple-600/20'
    }
}

function xIconColor() {
    switch (props.color) {
        case 'red':
            return 'stroke-red-700/50 group-hover:stroke-red-700/75'
        case 'green':
            return 'stroke-green-700/50 group-hover:stroke-green-700/75'
        case 'indigo':
            return 'stroke-indigo-700/50 group-hover:stroke-indigo-700/75'
        case 'yellow':
            return 'stroke-yellow-700/50 group-hover:stroke-yellow-700/75'
        case 'purple':
            return 'stroke-purple-700/50 group-hover:stroke-purple-700/75'
    }
}
</script>

<template>
    <span
        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
        :class="color()"
    >
        <p>
            {{ props.text }}
        </p>

        <button
            type="button"
            class="group relative ml-1 h-5 w-5 rounded-sm"
            :class="xIconHoverColor()"
            v-if="props.withDelete"
            @click="$emit('delete')"
            aria-hidden="true"
        >
            <svg
                viewBox="0 0 14 14"
                class="h-5 w-5"
                :class="xIconColor()"
            >
                <path d="M4 4l6 6m0-6l-6 6" />
                <span class="absolute -inset-1" />
            </svg>
        </button>
    </span>
</template>
