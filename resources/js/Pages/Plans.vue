<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Plans" />
    <MainLayout>
        <div class="flex flex-col gap-5">
            <h1 class="mb-5">Questions</h1>
            <p>
                1. How exactly do we go about creating the simplest case plan? (what variables? design @alex?,
                SelectionPage? toss or redesign?!)
            </p>
            <p>2. How is the MVP Plan calculated? Pull Pipedrive once a day and save to internal DB?!</p>
            <p>3. What variables do we save in the database, how?</p>
            <p>
                4. For the minimal plan, do Users have to select individual variables from Pipedrive, or do we use
                generic ones?
            </p>
            <p>5. What do we need from Pipedrive, and how do we name these variables internally?</p>
            <p>6. How is the plan connected to Users? The administrator can create users and assign them the plan?</p>
            <p>
                7. if 6., how is a user from the plan identified in Pipedrive? Admin has to link them to pipedrive on
                creation of user?!
            </p>
        </div>
    </MainLayout>
</template>
