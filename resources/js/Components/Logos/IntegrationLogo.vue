<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import { Component, computed } from 'vue'
import PipedriveLogo from './PipedriveLogo.vue'

const props = defineProps<{
    integrationName: IntegrationTypeEnum
}>()

const logos: Record<IntegrationTypeEnum, Component> = {
    pipedrive: PipedriveLogo,
}

const LogoToRender = computed(() => logos[props.integrationName])
</script>

<template>
    <LogoToRender />
</template>
