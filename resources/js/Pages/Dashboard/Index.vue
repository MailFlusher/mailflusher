<template>
  <div>
    <Head title="Dashboard" />

    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-grey-900 dark:text-white">Dashboard</h1>
      <p class="mt-1 text-sm text-grey-500 dark:text-grey-400">
        Welcome back, {{ $page.props.user?.username }}. Here's an overview of your account.
      </p>
    </div>

    <!-- Bandwidth warning -->
    <div
      v-if="bandwidthPercentage === 100"
      class="mb-6 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4"
    >
      <div class="flex items-center gap-3">
        <ExclamationTriangleIcon class="h-5 w-5 text-red-500 shrink-0" />
        <p class="text-sm font-medium text-red-800 dark:text-red-200">
          You've exceeded your bandwidth limit for <strong>{{ month }}</strong>.
          Emails will be rejected until the limit resets.
        </p>
      </div>
    </div>

    <!-- Suspected leaks -->
    <div
      v-if="leakEvents.length > 0"
      class="mb-6 rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 p-4"
    >
      <div class="flex items-start gap-3">
        <ExclamationTriangleIcon class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" />
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">
            {{ leakCount }} alias{{ leakCount === 1 ? '' : 'es' }} may have been leaked
          </h3>
          <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
            We detected mail from unexpected senders on these aliases. Confirm to treat as a leak; dismiss if it's legitimate.
          </p>
          <ul class="mt-3 space-y-2">
            <li
              v-for="event in leakEvents"
              :key="event.id"
              class="flex items-center justify-between gap-3 text-sm"
            >
              <span class="text-amber-900 dark:text-amber-100 truncate">
                Mail from <strong>{{ event.sender_domain }}</strong>
              </span>
              <span class="flex gap-2 shrink-0">
                <button
                  @click="resolveLeak(event.id, 'confirm')"
                  class="rounded px-2 py-1 text-xs font-medium bg-amber-600 hover:bg-amber-500 text-white"
                >Confirm</button>
                <button
                  @click="resolveLeak(event.id, 'dismiss')"
                  class="rounded px-2 py-1 text-xs font-medium bg-white dark:bg-grey-800 text-grey-700 dark:text-grey-200 border border-grey-200 dark:border-grey-700 hover:bg-grey-50 dark:hover:bg-grey-700"
                >Dismiss</button>
              </span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Quick stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
      <Link
        v-for="item in stats"
        :key="item.id"
        :href="item.url"
        class="group bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-5 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-sm transition-all"
      >
        <div class="flex items-center gap-3 mb-3">
          <div
            class="h-9 w-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center"
          >
            <component :is="item.icon" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
          </div>
        </div>
        <p class="text-2xl font-bold text-grey-900 dark:text-white">
          {{ item.stat.toLocaleString() }}
        </p>
        <p class="text-xs text-grey-500 dark:text-grey-400 mt-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
          {{ item.name }}
        </p>
      </Link>
    </div>

    <!-- Bandwidth -->
    <div class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-grey-900 dark:text-white">
          Bandwidth Usage
        </h2>
        <span class="text-sm text-grey-500 dark:text-grey-400">{{ month }}</span>
      </div>
      <div class="relative">
        <div class="overflow-hidden h-3 rounded-full bg-grey-100 dark:bg-grey-800">
          <div
            class="h-full rounded-full transition-all duration-500"
            :class="bandwidthBarClass"
            :style="`width: ${Math.max(bandwidthPercentage, 2)}%`"
          ></div>
        </div>
        <div class="flex items-center justify-between mt-2">
          <span class="text-xs font-medium text-grey-600 dark:text-grey-300">
            {{ bandwidthMb }} MB used
          </span>
          <span class="text-xs text-grey-400 dark:text-grey-500">
            {{ bandwidthLimit > 999999 ? 'Unlimited' : bandwidthLimit + ' MB limit' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Alias stats + Email stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Alias breakdown -->
      <div class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-6">
        <h2 class="text-sm font-semibold text-grey-900 dark:text-white mb-4">Aliases</h2>
        <div class="grid grid-cols-2 gap-4">
          <Link
            v-for="item in aliasStats"
            :key="item.id"
            :href="item.url"
            class="group rounded-lg border border-grey-100 dark:border-grey-700 p-4 hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors"
          >
            <div class="flex items-center gap-2 mb-2">
              <component
                :is="item.icon"
                class="h-4 w-4"
                :class="item.color"
              />
              <span class="text-xs font-medium text-grey-500 dark:text-grey-400">{{ item.name }}</span>
            </div>
            <p class="text-xl font-bold text-grey-900 dark:text-white">
              {{ item.stat.toLocaleString() }}
            </p>
          </Link>
        </div>
      </div>

      <!-- Email stats -->
      <div class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-6">
        <h2 class="text-sm font-semibold text-grey-900 dark:text-white mb-4">Email Activity</h2>
        <div class="grid grid-cols-2 gap-4">
          <div class="rounded-lg border border-grey-100 dark:border-grey-700 p-4">
            <p class="text-xs font-medium text-grey-500 dark:text-grey-400 mb-2">Forwarded</p>
            <p class="text-xl font-bold text-grey-900 dark:text-white">
              {{ parseInt(totals.forwarded).toLocaleString() }}
            </p>
          </div>
          <div class="rounded-lg border border-grey-100 dark:border-grey-700 p-4">
            <p class="text-xs font-medium text-grey-500 dark:text-grey-400 mb-2">Blocked</p>
            <p class="text-xl font-bold text-grey-900 dark:text-white">
              {{ parseInt(totals.blocked).toLocaleString() }}
            </p>
          </div>
          <div class="rounded-lg border border-grey-100 dark:border-grey-700 p-4">
            <p class="text-xs font-medium text-grey-500 dark:text-grey-400 mb-2">Replies</p>
            <p class="text-xl font-bold text-grey-900 dark:text-white">
              {{ parseInt(totals.replies).toLocaleString() }}
            </p>
          </div>
          <div class="rounded-lg border border-grey-100 dark:border-grey-700 p-4">
            <p class="text-xs font-medium text-grey-500 dark:text-grey-400 mb-2">Sent</p>
            <p class="text-xl font-bold text-grey-900 dark:text-white">
              {{ parseInt(totals.sent).toLocaleString() }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-sm font-semibold text-grey-900 dark:text-white">
          Outbound Messages — Last 7 Days
        </h2>
        <loader v-if="chartsLoading" />
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="flex justify-center max-h-72">
          <outbound-messages-graph
            :forwards-data="forwardsData"
            :replies-data="repliesData"
            :sends-data="sendsData"
            :labels="labels"
          />
        </div>
        <div class="flex justify-center items-center max-h-72">
          <div v-if="!outboundMessageTotals" class="text-sm text-grey-400 dark:text-grey-500">
            No data to display
          </div>
          <outbound-messages-pie v-else :totals="outboundMessageTotals" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import {
  AtSymbolIcon,
  InboxArrowDownIcon,
  UsersIcon,
  GlobeAltIcon,
  FunnelIcon,
  CheckCircleIcon,
  XCircleIcon,
  TrashIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import OutboundMessagesGraph from './OutboundMessagesGraph.vue'
import OutboundMessagesPie from './OutboundMessagesPie.vue'

const props = defineProps({
  totals: {
    type: Object,
    required: true,
  },
  bandwidthMb: {
    type: Number,
    required: true,
  },
  bandwidthLimit: {
    type: Number,
    required: true,
  },
  month: {
    type: String,
    required: true,
  },
  aliases: {
    type: Number,
    required: true,
  },
  recipients: {
    type: Number,
    required: true,
  },
  usernames: {
    type: Number,
    required: true,
  },
  domains: {
    type: Number,
    required: true,
  },
  rules: {
    type: Number,
    required: true,
  },
  pendingLeakEvents: {
    type: Array,
    default: () => [],
  },
  pendingLeakCount: {
    type: Number,
    default: 0,
  },
})

const leakEvents = ref(props.pendingLeakEvents)
const leakCount = ref(props.pendingLeakCount)

const resolveLeak = (id, action) => {
  axios
    .post(`/api/v1/leak-events/${id}/${action}`)
    .then(() => {
      leakEvents.value = leakEvents.value.filter(e => e.id !== id)
      leakCount.value = Math.max(0, leakCount.value - 1)
    })
    .catch(() => {
      /* non-blocking */
    })
}

const chartsLoading = ref(true)
const forwardsData = ref([])
const repliesData = ref([])
const sendsData = ref([])
const labels = ref([])
const outboundMessageTotals = ref(null)

onMounted(() => {
  axios.get('/api/v1/chart-data').then(response => {
    forwardsData.value = response.data.forwardsData
    repliesData.value = response.data.repliesData
    sendsData.value = response.data.sendsData
    labels.value = response.data.labels
    outboundMessageTotals.value = response.data.outboundMessageTotals

    if (_.isEqual(outboundMessageTotals.value, [0, 0, 0])) {
      outboundMessageTotals.value = null
    }

    chartsLoading.value = false
  })
})

const bandwidthPercentage = computed(() => {
  if (props.bandwidthLimit > 999999) return 0
  if (props.bandwidthMb) {
    let percent = ((props.bandwidthMb / props.bandwidthLimit) * 100).toFixed(2)
    return percent > 100 ? 100 : percent
  }
  return 0
})

const bandwidthBarClass = computed(() => {
  if (bandwidthPercentage.value >= 100) return 'bg-red-500'
  if (bandwidthPercentage.value > 80) return 'bg-yellow-500'
  return 'bg-indigo-500'
})

const stats = [
  {
    id: 1,
    name: 'Aliases',
    stat: props.aliases,
    icon: AtSymbolIcon,
    url: route('aliases.index'),
  },
  {
    id: 2,
    name: 'Recipients',
    stat: props.recipients,
    icon: InboxArrowDownIcon,
    url: route('recipients.index'),
  },
  {
    id: 3,
    name: 'Usernames',
    stat: props.usernames,
    icon: UsersIcon,
    url: route('usernames.index'),
  },
  {
    id: 4,
    name: 'Domains',
    stat: props.domains,
    icon: GlobeAltIcon,
    url: route('domains.index'),
  },
  {
    id: 5,
    name: 'Rules',
    stat: props.rules,
    icon: FunnelIcon,
    url: route('rules.index'),
  },
]

const aliasStats = [
  {
    id: 1,
    name: 'Total',
    stat: parseInt(props.totals.total),
    icon: AtSymbolIcon,
    color: 'text-indigo-500',
    url: route('aliases.index', { deleted: 'with' }),
  },
  {
    id: 2,
    name: 'Active',
    stat: parseInt(props.totals.active),
    icon: CheckCircleIcon,
    color: 'text-green-500',
    url: route('aliases.index', { active: 'true' }),
  },
  {
    id: 3,
    name: 'Inactive',
    stat: parseInt(props.totals.inactive),
    icon: XCircleIcon,
    color: 'text-yellow-500',
    url: route('aliases.index', { active: 'false' }),
  },
  {
    id: 4,
    name: 'Deleted',
    stat: parseInt(props.totals.deleted),
    icon: TrashIcon,
    color: 'text-red-500',
    url: route('aliases.index', { deleted: 'only' }),
  },
]
</script>
