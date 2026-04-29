<template>
  <Link
    :href="route('subscription.index')"
    class="block rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 p-3 text-white transition-colors"
  >
    <div class="flex items-start gap-2">
      <SparklesIcon class="h-4 w-4 text-cyan-300 shrink-0 mt-0.5" />
      <div class="flex-1 min-w-0">
        <p class="text-xs font-semibold leading-tight">{{ title }}</p>
        <p class="text-[11px] text-indigo-100 mt-0.5 leading-snug">{{ subtitle }}</p>
      </div>
    </div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { SparklesIcon } from '@heroicons/vue/24/solid'

defineProps({
  title: { type: String, default: 'Upgrade to unlock more' },
  subtitle: { type: String, default: 'Starting at €1/month' },
})
</script>
