<script setup lang="ts">
import PrimaryButton from './Buttons/PrimaryButton.vue'

const props = defineProps<{
    title: string
    description: string
    buttonText?: string
    noBottomMargin?: boolean
    buttonDuskIdentifier?: string
}>()
</script>

<template>
    <div
        class="sm:flex sm:items-center"
        :class="props.noBottomMargin ? 'mb-0' : 'mb-10'"
    >
        <div class="sm:flex-auto">
            <h3 class="leading-6 text-gray-900">{{ props.title }}</h3>
            <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700">{{ props.description }}</p>
        </div>

        <div
            class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none"
            v-if="props.buttonText"
        >
            <PrimaryButton
                :dusk="props.buttonDuskIdentifier"
                @click="$emit('button-clicked')"
                :text="props.buttonText"
            />
        </div>

        <slot name="custom-button" />
    </div>
</template>
