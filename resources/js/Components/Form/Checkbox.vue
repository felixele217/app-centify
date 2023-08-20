<script setup lang="ts">
import { computed } from 'vue'

const emit = defineEmits(['update:checked'])

const props = defineProps<{
    checked: boolean
    label: string
    value?: any
}>()

const proxyChecked = computed({
    get() {
        return props.checked
    },

    set(val) {
        emit('update:checked', val)
    },
})
</script>

<template>
    <div>
        <label
            class="items-top flex gap-2"
            for="checkbox"
        >
            <input
                name="checkbox"
                type="checkbox"
                :value="value"
                v-model="proxyChecked"
                class="cursor-pointer rounded border-gray-300 text-primary shadow-sm focus:ring-primary-hover"
            />

            <p class="-mt-0.5 whitespace-pre-wrap text-sm text-gray-700">{{ props.label }}</p>
        </label>
    </div>
</template>
