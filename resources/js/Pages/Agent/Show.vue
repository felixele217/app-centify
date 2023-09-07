<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import euroDisplay from '@/utils/euroDisplay'
import DoughnutChart from '@/Components/Dashboard/Payout/DoughnutChart/Content.vue'
import Card from '@/Components/Card.vue'
import roundFloat from '@/utils/roundFloat'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'
import TotalCommission from '@/Components/Dashboard/Payout/TotalCommission.vue'
import { BanknotesIcon, SunIcon } from '@heroicons/vue/24/outline'
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import { computed } from 'vue'
import PaidLeaveCard from '@/Components/Agent/PaidLeave/PaidLeaveCard.vue'
import sum from '@/utils/sum'
import SickIcon from '@/Components/Icon/SickIcon.vue'
import Header from '@/Components/Agent/Show/Header.vue'
import CommissionDeepDive from '@/Components/Agent/Show/CommissionDeepDive.vue'
import QuotaDeepDive from '@/Components/Agent/Show/QuotaDeepDive.vue'
import AgentVue from '@/Components/Agent/Show/Agent.vue'
import Agent from '@/types/Agent'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <Card class="w-240"> <AgentVue :agent="props.agent" /> </Card>
</template>
