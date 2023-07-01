<script setup lang="ts">
import PrimaryButton from './Buttons/PrimaryButton.vue'

const props = defineProps<{
    title: string
    description: string
    buttonText: string
}>()

const emit = defineEmits(['button-clicked'])
</script>

<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ props.title }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ props.description }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <PrimaryButton
                @click="emit('button-clicked')"
                :text="props.buttonText"
            />
        </div>
    </div>
</template>
