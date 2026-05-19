<template>
  <Link
    v-if="show"
    :href="route('subscription.index')"
    class="block bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white transition-colors"
  >
    <div
      class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-3 text-sm"
    >
      <div class="flex items-center gap-2 min-w-0">
        <SparklesIcon class="h-4 w-4 text-cyan-300 shrink-0" />
        <span class="font-medium">
          {{ daysRemaining }} {{ daysRemaining === 1 ? 'day' : 'days' }} of
          {{ $page.props.user?.plan_name }} trial left
        </span>
        <span class="hidden sm:inline text-indigo-100">
          — trial ends {{ formattedEndDate }}
        </span>
      </div>
      <span class="font-semibold underline-offset-2 hover:underline shrink-0"
        >Upgrade →</span
      >
    </div>
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { SparklesIcon } from '@heroicons/vue/24/solid'

const page = usePage()

const trialEndsAt = computed(() => {
  const iso = page.props.user?.trial_ends_at
  return iso ? new Date(iso) : null
})

const show = computed(
  () => !!page.props.user?.on_trial && trialEndsAt.value && trialEndsAt.value > new Date(),
)

const daysRemaining = computed(() => {
  if (!trialEndsAt.value) return 0
  const msPerDay = 24 * 60 * 60 * 1000
  return Math.max(1, Math.ceil((trialEndsAt.value - new Date()) / msPerDay))
})

const formattedEndDate = computed(() => {
  if (!trialEndsAt.value) return ''
  return trialEndsAt.value.toLocaleDateString(undefined, {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
})
</script>
