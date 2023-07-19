<script setup lang="ts">
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import { FunctionalComponent } from 'vue'
import Dropdown from './Dropdown.vue'
import DropdownBox from './DropdownBox.vue'

const props = defineProps<{
    icon?: FunctionalComponent
}>()

defineEmits(['edit-action', 'delete-action'])
</script>

<template>
    <Dropdown>
        <template #trigger>
            <component
                :is="props.icon"
                v-if="props.icon"
                class="h-5 w-5 cursor-pointer text-gray-700 hover:text-black"
            />

            <EllipsisVerticalIcon
                class="h-5 w-5 cursor-pointer text-gray-700 hover:text-black"
                v-else
            />
        </template>

        <template #content>
            <DropdownBox
                class="rounded-t-sm"
                text="Edit"
                @click="$emit('edit-action')"
            />
            <DropdownBox
                class="rounded-b-sm"
                text="Delete"
                @click="$emit('delete-action')"
            />
        </template>
    </Dropdown>
</template>
