<script setup lang="ts">
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue'
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: boolean
    title: string
    description: string
    color?: 'red' | 'indigo'
}>()

const emit = defineEmits<{
    'update:modelValue': [newValue: boolean]
}>()

const enabled = ref(props.modelValue)

watch(
    () => enabled.value,
    (newValue) => {
        emit('update:modelValue', newValue)
    }
)

const toggleColor =
    props.color === 'red'
        ? 'bg-red-600 focus:ring-red-600 focus:ring-2 focus:ring-offset-2'
        : 'bg-indigo-600 focus:ring-indigo-600 focus:ring-2 focus:ring-offset-2'
</script>

<template>
    <SwitchGroup
        as="div"
        class="flex items-center justify-between"
    >
        <span class="flex flex-grow flex-col">
            <SwitchLabel
                as="span"
                class="text-sm font-medium leading-6 text-gray-900"
                passive
            >
                {{ props.title }}
            </SwitchLabel>

            <SwitchDescription
                as="span"
                class="text-sm text-gray-500"
            >
                {{ props.description }}
            </SwitchDescription>
        </span>
        <Switch
            v-model="enabled"
            :class="[
                enabled ? toggleColor : 'bg-gray-200',
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ',
            ]"
        >
            <span
                aria-hidden="true"
                :class="[
                    enabled ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                ]"
            />
        </Switch>
    </SwitchGroup>
</template>
