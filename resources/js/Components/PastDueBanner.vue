<template>
  <a
    v-if="show"
    :href="route('subscription.portal')"
    class="block bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white transition-colors"
  >
    <div
      class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-3 text-sm"
    >
      <div class="flex items-center gap-2 min-w-0">
        <ExclamationTriangleIcon class="h-4 w-4 text-yellow-200 shrink-0" />
        <span class="font-medium">
          Your last payment failed.
        </span>
        <span class="hidden sm:inline text-red-100">
          Update your card to keep your {{ $page.props.user?.plan_name }} plan active.
        </span>
      </div>
      <span class="font-semibold underline-offset-2 hover:underline shrink-0"
        >Update payment →</span
      >
    </div>
  </a>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid'

const page = usePage()

const show = computed(() => !!page.props.user?.past_due)
</script>
