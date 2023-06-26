<script setup lang="ts">
import DropdownLink from '@/Components/Dropdown/DropdownLink.vue'
import Sidebar from '@/Components/MainLayout/Sidebar.vue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { BellIcon, ChartPieIcon, FolderIcon, HomeIcon, PuzzlePieceIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'
import { computed } from '@vue/reactivity'

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    { name: 'Teams & Users', href: '#', icon: FolderIcon, current: false },
    { name: 'Plans', href: route('plans'), icon: ChartPieIcon, current: route().current('plans') },
    {
        name: 'Integrations',
        href: route('integrations'),
        icon: PuzzlePieceIcon,
        current: route().current('integrations'),
    },
]

const user = computed(() => usePage().props.auth.user)

const userNavigation = [
    { name: 'Your profile', href: route('profile.edit') },
    { name: 'Sign out', href: route('logout'), method: 'post' },
]
</script>

<template>
    <div>
        <Sidebar :navigation="navigation" />

        <div class="flex h-screen flex-col lg:pl-72">
            <div
                class="flex h-16 flex-1 items-center gap-x-4 self-stretch border-b border-gray-200 bg-white px-8 py-3 shadow-sm lg:gap-x-6"
            >
                <form
                    class="relative flex flex-1"
                    action="#"
                    method="GET"
                >
                    <label
                        for="search-field"
                        class="sr-only"
                        >Search</label
                    >
                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                        aria-hidden="true"
                    />
                    <input
                        id="search-field"
                        class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                        placeholder="Search..."
                        type="search"
                        name="search"
                    />
                </form>
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <button
                        type="button"
                        class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">View notifications</span>
                        <BellIcon
                            class="h-6 w-6"
                            aria-hidden="true"
                        />
                    </button>

                    <!-- Separator -->
                    <div
                        class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10"
                        aria-hidden="true"
                    />

                    <!-- Profile dropdown -->
                    <Menu
                        as="div"
                        class="relative"
                    >
                        <MenuButton class="-m-1.5 flex items-center p-1.5">
                            <span class="sr-only">Open user menu</span>
                            <img
                                class="h-8 w-8 rounded-full bg-gray-50"
                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                alt=""
                            />
                            <span class="ml-4 hidden gap-2 lg:flex lg:items-center">
                                <div class="flex flex-col items-start">
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                        aria-hidden="true"
                                    >
                                        {{ user.name }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ user.email }}</p>
                                </div>
                                <ChevronDownIcon
                                    class="ml-2 h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                            </span>
                        </MenuButton>
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <MenuItems
                                class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                            >
                                <MenuItem
                                    v-for="item in userNavigation"
                                    :key="item.name"
                                    v-slot="{ active }"
                                >
                                    <DropdownLink
                                        :method="item.method"
                                        as="button"
                                        :href="item.href"
                                        :class="[
                                            active ? 'bg-gray-50' : '',
                                            'block px-3 py-1 text-sm leading-6 text-gray-900',
                                        ]"
                                        >{{ item.name }}
                                    </DropdownLink>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>

            <main class="grow p-5 sm:px-6 lg:px-10">
                <slot />
            </main>
        </div>
    </div>
</template>
