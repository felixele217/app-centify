<script setup lang="ts">
import NavigationItem from '@/types/NavigationItem'
import { ArrowLeftOnRectangleIcon, Cog6ToothIcon, UserIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from '@inertiajs/vue3'
import Dropdown from '../Dropdown/Dropdown.vue'
import SidebarLink from './SidebarLink.vue'

const props = defineProps<{
    navigation: Array<NavigationItem>
}>()
</script>

<template>
    <Dropdown
        align="top"
        class="mt-auto"
    >
        <template #trigger>
            <SidebarLink
                :name="usePage().props.auth.user.name"
                :icon="UserIcon"
                :current="route().current('profile.edit')"
            />
        </template>

        <template #content>
            <a
                :href="route('profile.edit')"
                class="flex cursor-pointer items-center gap-2 rounded-t-md p-3 hover:bg-gray-100"
            >
                <Cog6ToothIcon class="h-5 w-5" />
                <p class="">Settings</p>
            </a>
            <div
                @click="router.post(route('logout'))"
                class="flex cursor-pointer items-center gap-2 rounded-b-md p-3 hover:bg-gray-100"
            >
                <ArrowLeftOnRectangleIcon class="h-5 w-5" />
                <p class="">Log out</p>
            </div>
        </template>
    </Dropdown>
</template>
