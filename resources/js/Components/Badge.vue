<script setup lang="ts">
const props = defineProps<{
    text: string
    color: 'green' | 'red' | 'indigo' | 'yellow' | 'purple'
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
    <p
        :class="color()"
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
    >
        {{ props.text }}
    </p>
</template>
