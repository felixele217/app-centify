<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'

const props = withDefaults(
    defineProps<{
        align?: 'left' | 'right' | 'top'
        width?: '48' | '64'
        contentClasses?: string
        closeOnContentClick?: boolean
        isOpen?: boolean | null
    }>(),
    {
        align: 'right',
        width: '48',
        contentClasses: 'rounded-md bg-white ',
        closeOnContentClick: true,
        isOpen: null,
    }
)

const closeOnEscape = (e: KeyboardEvent) => {
    if (open.value && e.key === 'Escape') {
        open.value = false
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

const widthClass = computed(() => {
    return {
        48: 'w-48',
        64: 'w-64',
    }[props.width.toString()]
})

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0'
    } else if (props.align === 'right') {
        return 'origin-top-right right-0'
    } else if (props.align === 'top') {
        return 'origin-bottom bottom-full'
    } else {
        return 'origin-top'
    }
})

const emit = defineEmits<{
    'set-is-open': [state: boolean]
}>()

const open = ref(props.isOpen)
function handleContentClick() {
    if (props.closeOnContentClick) {
        closeDropdown()
    }
}

const closeDropdown = () => {
    if (props.isOpen === null) {
        open.value = false
    } else {
        emit('set-is-open', false)
    }
}

const toggleDropdown = () => {
    if (props.isOpen === null) {
        open.value = !open.value
    } else {
        emit('set-is-open', !props.isOpen)
    }
}
</script>

<template>
    <div class="relative">
        <div @click="toggleDropdown">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open || props.isOpen"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
        />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open || props.isOpen"
                class="absolute z-50 mt-2 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="handleContentClick"
            >
                <div :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
