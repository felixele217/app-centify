<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import { Component, computed } from 'vue'
import PipedriveLogo from './PipedriveLogo.vue'

const props = defineProps<{
    integration: IntegrationTypeEnum
}>()

const logos: Record<IntegrationTypeEnum, Component> = {
    pipedrive: PipedriveLogo,
}

const LogoToRender = computed(() => logos[props.integration])
</script>

<template>
    <LogoToRender />
</template>
