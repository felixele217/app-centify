<script setup lang="ts">
import EmployeeCard from './EmployeeCard.vue'
import Card from '@/Components/Card.vue';

const employees = [
    { photo: null, name: 'Jerome Bell', role: 'Account Executive', commission: 8592.02, change: 2.5 },
    { photo: null, name: 'Wade Warren', role: 'Sr. Account Executive', commission: 5102.02, change: -3.4 },
    { photo: null, name: 'Jerome Bell', role: 'Account Executive', commission: 8592.02, change: 2.5 },
    { photo: null, name: 'Wade Warren', role: 'Sr. Account Executive', commission: 5102.02, change: -3.4 },
    { photo: null, name: 'Jerome Bell', role: 'Account Executive', commission: 8592.02, change: 2.5 },
    { photo: null, name: 'Wade Warren', role: 'Sr. Account Executive', commission: 5102.02, change: -3.4 },
    { photo: null, name: 'Jerome Bell', role: 'Account Executive', commission: 8592.02, change: 2.5 },
    { photo: null, name: 'Wade Warren', role: 'Sr. Account Executive', commission: 5102.02, change: -3.4 },
    { photo: null, name: 'Jerome Bell', role: 'Account Executive', commission: 8592.02, change: 2.5 },
]
</script>

<template>
    <Card>
        <p class="font-semibold mb-3">Total Payout by Employee</p>
        <div class="grid grid-cols-7 bg-gray-100 px-5 font-semibold rounded-xl py-1 text-sm">
            <p class="col-span-3">Employee</p>
            <p class="col-span-2">Commission</p>
            <p class="col-span-2">vs. last quarter</p>
        </div>
            <EmployeeCard
                v-for="employee in employees"
                v-bind="employee"
            />
    </Card>
</template>
