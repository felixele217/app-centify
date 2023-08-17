<script setup lang="ts">
import Popper from 'vue3-popper'

const props = withDefaults(
    defineProps<{
        text: string
        placement?: 'left' | 'right' | 'top' | 'bottom'
    }>(),
    {
        placement: 'left',
    }
)
</script>

<template>
    <Popper
        :open-delay="200"
        hover
        arrow
        :placement="props.placement"
        :content="props.text"
        class="text-sm"
        :z-index="9999"
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
    --popper-theme-padding: 16px;
    --popper-theme-box-shadow: 0 6px 30px -6px rgba(0, 0, 0, 0.25);
}
</style>
