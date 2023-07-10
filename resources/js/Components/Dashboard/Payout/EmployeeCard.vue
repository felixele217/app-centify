<script setup lang="ts">
const props = defineProps<{
    photo: any
    name: string
    role: string
    commission: number
    change: number
}>()
</script>

<template>
    <div class="mt-3 grid grid-cols-7 items-center px-5">
        {{ props.photo }}

        <div class="col-span-3">
            <p class="font-semibold">{{ props.name }}</p>
            <p class="-mt-1.5 text-sm font-semibold text-gray-400">
                {{ props.role }}
            </p>
        </div>

        <p class="col-span-2 font-semibold">{{ props.commission }}€</p>

        <p class="col-span-2 font-semibold">{{ props.change }}%</p>
    </div>
</template>
