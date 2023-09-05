<script setup lang="ts">
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import { FunctionalComponent } from 'vue'
import Dropdown from './Dropdown.vue'
import DropdownBox from './DropdownBox.vue'

const props = defineProps<{
    icon?: FunctionalComponent
    relativeTableStyle?: string
}>()

defineEmits(['edit-action', 'delete-action'])
</script>

<template>
    <Dropdown :class="props.relativeTableStyle || ''">
        <template #trigger>
            <component
                :is="props.icon"
                v-if="props.icon"
                class="h-5 w-5 cursor-pointer text-gray-700 hover:text-black"
            />

            <EllipsisVerticalIcon
                class="h-7 w-7 cursor-pointer rounded-full p-1 text-gray-700 hover:bg-slate-50 hover:text-black"
                v-else
            />
        </template>

        <template #content>
            <div class="rounded-md shadow-lg ring-1 ring-gray-900/5">
                <DropdownBox
                    class="rounded-t-md"
                    text="Edit"
                    @click="$emit('edit-action')"
                />
                <DropdownBox
                    class="rounded-b-md"
                    text="Delete"
                    @click="$emit('delete-action')"
                />
            </div>
        </template>
    </Dropdown>
</template>
