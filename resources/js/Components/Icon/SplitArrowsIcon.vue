<script setup lang="ts">
import { ref } from 'vue'

const fillColor = ref<string>('#64748b')

const changeFillOnHover = (hover: boolean) => (fillColor.value = hover ? '#1e293b' : '#64748b')
</script>

<template>
    <div
        class="h-5 w-5 -rotate-90"
        @mouseover="changeFillOnHover(true)"
        @mouseleave="changeFillOnHover(false)"
    >
        <svg
            id="Layer_1"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 512 512"
            :fill="fillColor"
        >
            <g>
                <path
                    class="st0"
                    d="M256,0c15.71,0,28.44,12.74,28.44,28.44v142.22h64c43.2,0,78.22,35.02,78.22,78.22v166l36.78-36.78
                c11.11-11.11,29.12-11.11,40.23,0c11.11,11.11,11.11,29.12,0,40.23l-85.33,85.33c-11.11,11.11-29.12,11.11-40.23,0l-85.33-85.33
                c-11.11-11.11-11.11-29.12,0-40.23s29.12-11.11,40.23,0l36.78,36.78v-166c0-11.78-9.55-21.33-21.33-21.33H163.56
                c-11.78,0-21.33,9.55-21.33,21.33v166L179,378.11c11.11-11.11,29.12-11.11,40.23,0c11.11,11.11,11.11,29.12,0,40.23l-85.33,85.33
                c-11.11,11.11-29.12,11.11-40.23,0L8.33,418.34c-11.11-11.11-11.11-29.12,0-40.23c11.11-11.11,29.12-11.11,40.23,0l36.78,36.78
                v-166c0-43.2,35.02-78.22,78.22-78.22h64V28.44C227.56,12.74,240.29,0,256,0z"
                />
            </g>
        </svg>
    </div>
</template>
