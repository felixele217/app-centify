<script setup lang="ts">
import Popper from 'vue3-popper'

const props = defineProps<{
    text: string
}>()
</script>

<template>
    <Popper
        hover
        arrow
        placement="left"
        :content="props.text"
    >
        <slot />
    </Popper>
</template>

<style>
:root {
    --popper-theme-background-color: #333333;
    --popper-theme-background-color-hover: #333333;
    --popper-theme-text-color: #ffffff;
    --popper-theme-border-width: 0px;
    --popper-theme-border-style: solid;
    --popper-theme-border-radius: 6px;
    --popper-theme-padding: 24px;
    --popper-theme-box-shadow: 0 6px 30px -6px rgba(0, 0, 0, 0.25);
    font-size: 0.875rem;
}
</style>
