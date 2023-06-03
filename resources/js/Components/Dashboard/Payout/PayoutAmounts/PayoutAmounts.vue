<script setup lang="ts">
import Card from '../../../Card.vue'
import { subtleText } from '../../styles'
import Current from './Current.vue'
import Possible from './Possible.vue'
</script>

<template>
    <Card>
        <Current />

        <Possible />

        <div>
            <p :class="subtleText">last synchronized</p>
            <p class="text-sm font-bold text-gray-800">11:28 - 09.06.2023</p>
        </div>
    </Card>
</template>
