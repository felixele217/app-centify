<script setup lang="ts">
import { CheckIcon } from '@heroicons/vue/20/solid'

const timeline = [
    {
        id: 1,
        content: 'Choose template',
        href: '#',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
    {
        id: 2,
        content: 'Fill out form',
        href: '#',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
    {
        id: 3,
        content: 'Simulation',
        href: '#',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
    {
        id: 4,
        content: 'Review',
        href: '#',
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
    },
]
</script>

<template>
    <div class="flow-root">
        <ul role="list">
            <li
                v-for="(event, eventIdx) in timeline"
                :key="event.id"
            >
                <div class="relative pb-8">
                    <span
                        v-if="eventIdx !== timeline.length - 1"
                        class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                        aria-hidden="true"
                    />
                    <div class="relative flex space-x-3">
                        <div>
                            <span
                                :class="[
                                    event.iconBackground,
                                    'flex h-8 w-8 items-center justify-center rounded-full ring-4 ring-white',
                                ]"
                            >
                                <component
                                    :is="event.icon"
                                    class="h-5 w-5 text-white"
                                    aria-hidden="true"
                                />
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ event.content }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
