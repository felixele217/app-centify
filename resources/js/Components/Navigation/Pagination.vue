<script setup lang="ts">
import PaginatedObject from '@/types/PaginatedObject'
import queryParamValue from '@/utils/queryParamValue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

const props = defineProps<{
    paginatedObject: PaginatedObject
}>()
</script>

<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-5 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <a
                v-if="props.paginatedObject.prev_page_url"
                :href="props.paginatedObject.prev_page_url"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >Previous</a
            >
            <a
                v-if="props.paginatedObject.next_page_url"
                :href="props.paginatedObject.next_page_url"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >Next</a
            >
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    {{ ' ' }}
                    <span class="font-medium">{{ props.paginatedObject.from }}</span>
                    {{ ' ' }}
                    to
                    {{ ' ' }}
                    <span class="font-medium">{{ props.paginatedObject.to }}</span>
                    {{ ' ' }}
                    of
                    {{ ' ' }}
                    <span class="font-medium">{{ props.paginatedObject.total }}</span>
                    {{ ' ' }}
                    results
                </p>
            </div>
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <a
                        v-if="props.paginatedObject.prev_page_url"
                        :href="props.paginatedObject.prev_page_url"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    >
                        <ChevronLeftIcon
                            class="h-5 w-5"
                            aria-hidden="true"
                        />
                    </a>
                    <a
                        v-for="n in props.paginatedObject.last_page"
                        :key="n"
                        :href="props.paginatedObject.links[n].url"
                        aria-current="page"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold"
                        :class="
                            queryParamValue('page') === n.toString() || (queryParamValue('page') === '' && n == 1)
                                ? 'z-10 bg-primary text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary'
                                : 'text-black ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'
                        "
                    >
                        {{ n }}
                    </a>

                    <a
                        v-if="props.paginatedObject.next_page_url"
                        :href="props.paginatedObject.next_page_url"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    >
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon
                            class="h-5 w-5"
                            aria-hidden="true"
                        />
                    </a>
                </nav>
            </div>
        </div>
    </div>
</template>
