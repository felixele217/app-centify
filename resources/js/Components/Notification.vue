<script setup lang="ts">
import { CheckCircleIcon, ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { Notification, NotificationGroup } from 'notiwind'
</script>

<template>
    <NotificationGroup group="centify">
        <div class="pointer-events-none fixed inset-0 flex items-start justify-end p-6 px-4 py-6">
            <div class="w-full max-w-sm">
                <Notification
                    v-slot="{ notifications, close }"
                    enter="transform ease-out duration-300 transition"
                    enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                    enter-to="translate-y-0 opacity-100 sm:translate-x-0"
                    leave="transition ease-in duration-500"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                    move="transition duration-500"
                    move-delay="delay-300"
                >
                    <div
                        class="mx-auto mt-4 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md"
                        v-for="notification in notifications"
                        :key="notification.id"
                    >
                        <div
                            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                        >
                            <div class="p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <CheckCircleIcon
                                            v-if="notification.wasSuccessful"
                                            class="h-6 w-6 text-green-400"
                                            aria-hidden="true"
                                        />
                                        <ExclamationCircleIcon
                                            v-else
                                            class="h-6 w-6 text-red-500"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div class="ml-3 w-0 flex-1 pt-0.5">
                                        <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ notification.text }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex flex-shrink-0">
                                        <button
                                            type="button"
                                            class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500"
                                        >
                                            <span class="sr-only">Close</span>
                                            <XMarkIcon
                                                @click="close(notification.id)"
                                                class="h-5 w-5"
                                                aria-hidden="true"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Notification>
            </div>
        </div>
    </NotificationGroup>
</template>
