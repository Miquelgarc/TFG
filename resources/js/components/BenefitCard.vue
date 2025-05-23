<template>
  <div class="bg-white/10 p-6 rounded-xl shadow-md hover:scale-105 transition">
    <div class="text-4xl mb-3">{{ icon }}</div>
    <h3 class="text-xl font-semibold text-yellow-300">{{ title }}</h3>
    <p class="text-white/80">{{ text }}</p>
  </div>
</template>

<script setup lang="ts">
defineProps({
  title: String,
  text: String,
  icon: String,
})
</script>
