<script setup lang="ts">
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import Dropdown from './Dropdown.vue'
import DropdownBox from './DropdownBox.vue'

defineEmits(['edit-action', 'delete-action'])
</script>

<template>
    <Dropdown>
        <template #trigger>
            <EllipsisVerticalIcon class="invisible h-5 w-5 cursor-pointer text-gray-700 group-hover:visible" />
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
