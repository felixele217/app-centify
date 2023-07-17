<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import { computed } from 'vue'
import PipedriveLogo from './PipedriveLogo.vue'

const props = defineProps<{
    for: IntegrationTypeEnum
}>()

const logos: Record<IntegrationTypeEnum, any> = {
    pipedrive: PipedriveLogo,
}

const LogoToRender = computed(() => logos[props.for])
</script>

<template>
    <LogoToRender />
</template>
