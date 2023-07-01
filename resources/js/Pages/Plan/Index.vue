<script setup lang='ts'>
import type Plan from '@/types/Plan';


const props = defineProps<{
   plans: Array<Plan>
}>()

</script>

<template>
    <PlanCard :plan="props.plans[0]"/>
</template>
