<script setup lang="ts">
const deals = [
    { index: 1, company: 'Centify GmbH', arr: 1000000, commission: 1000 },
    { index: 2, company: 'Nulin GmbH', arr: 22789, commission: 516 },
    { index: 3, company: 'Some GmbH', arr: 35120, commission: 816 },
    { index: 4, company: 'Thalia GmbH', arr: 18421, commission: 342 },
    { index: 5, company: 'Trixo GmbH', arr: 19843, commission: 412 },
]
</script>

<template>
    <div class="mb-2 grid grid-cols-10 font-bold">
        <p class="col-span-1"></p>
        <p class="col-span-4">Company</p>
        <p class="col-span-2 text-right">ARR</p>
        <p class="col-span-3 text-right">Commission</p>
    </div>
    <div
        class="mb-2 grid grid-cols-10 items-center"
        v-for="deal in deals"
    >
        <p class="col-span-1 h-5 w-5 rounded-full bg-violet-primary text-center text-sm text-white">{{ deal.index }}</p>
        <p class="col-span-4">{{ deal.company }}</p>
        <p class="col-span-2 text-right">{{ deal.arr }}€</p>
        <p class="col-span-3 text-right">{{ deal.commission }}€</p>
    </div>
</template>
