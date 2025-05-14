<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" :class="className" v-bind="$attrs">
        <g fill="none" stroke="currentColor" stroke-width="2">
            <!-- Base structure -->
            <rect x="4" y="44" width="16" height="16" rx="2" />
            <rect x="24" y="44" width="16" height="16" rx="2" />
            <rect x="44" y="44" width="16" height="16" rx="2" />

            <!-- Middle nodes -->
            <circle cx="16" cy="28" r="4" fill="currentColor" />
            <circle cx="32" cy="20" r="4" fill="currentColor" />
            <circle cx="48" cy="28" r="4" fill="currentColor" />

            <!-- Connections -->
            <line x1="16" y1="28" x2="32" y2="20" />
            <line x1="48" y1="28" x2="32" y2="20" />
            <line x1="16" y1="28" x2="12" y2="44" />
            <line x1="48" y1="28" x2="52" y2="44" />
        </g>
    </svg>
</template>
