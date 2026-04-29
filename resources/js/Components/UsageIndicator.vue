<template>
  <div>
    <div class="flex items-center justify-between text-xs mb-1">
      <span class="font-medium text-grey-600 dark:text-grey-300">{{ label }}</span>
      <span :class="[near ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-grey-500 dark:text-grey-400']">
        {{ count }}<template v-if="hasLimit">&nbsp;/&nbsp;{{ limit }}</template>
      </span>
    </div>
    <div v-if="hasLimit" class="h-1.5 w-full bg-grey-100 dark:bg-grey-800 rounded-full overflow-hidden">
      <div
        :class="[
          'h-full rounded-full transition-all',
          atLimit ? 'bg-red-500' : near ? 'bg-amber-500' : 'bg-indigo-500',
        ]"
        :style="{ width: percent + '%' }"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  count: { type: Number, required: true },
  limit: { type: [Number, null], default: null },
})

const hasLimit = computed(() => props.limit !== null && props.limit !== undefined)
const percent = computed(() => {
  if (!hasLimit.value || props.limit === 0) return 0
  return Math.min(100, Math.round((props.count / props.limit) * 100))
})
const near = computed(() => hasLimit.value && percent.value >= 80)
const atLimit = computed(() => hasLimit.value && props.count >= props.limit)
</script>
