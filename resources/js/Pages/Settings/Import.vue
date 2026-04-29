<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <div class="pt-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
            Import aliases from another service
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            Moving in from SimpleLogin or Addy.io? Paste your API token and we'll fetch your
            existing aliases and recreate them on your MailFlusher subdomain. Only the description
            and active state are copied — the new aliases will be on your
            <code class="bg-grey-100 dark:bg-grey-800 px-1 rounded font-mono">{{ defaultDomain }}</code>
            domain, so the email addresses themselves change.
          </p>
          <p class="text-sm text-amber-700 dark:text-amber-300 !mt-3">
            <b>Heads up:</b> your existing aliases at the source service keep working there unless
            you deactivate them. This is a copy, not a move.
          </p>
        </div>

        <!-- Service picker -->
        <div class="mt-6">
          <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-2">
            Source service
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <button
              v-for="s in services"
              :key="s.slug"
              @click="selectService(s.slug)"
              :disabled="s.disabled"
              :class="[
                'rounded-lg border p-4 text-left transition-colors',
                s.disabled
                  ? 'opacity-50 cursor-not-allowed border-grey-200 dark:border-grey-700'
                  : selectedService === s.slug
                    ? 'border-indigo-500 ring-2 ring-indigo-200 bg-indigo-50 dark:bg-indigo-900/20'
                    : 'border-grey-200 dark:border-grey-700 hover:border-indigo-300',
              ]"
            >
              <p class="font-semibold text-grey-900 dark:text-white">{{ s.name }}</p>
              <p class="text-xs text-grey-500 dark:text-grey-400 mt-1">{{ s.hint }}</p>
            </button>
          </div>
        </div>

        <!-- Token field -->
        <div v-if="selectedService" class="mt-6">
          <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-2">
            {{ selectedService === 'simplelogin' ? 'SimpleLogin API key' : 'Addy.io API token' }}
          </label>
          <input
            v-model="token"
            type="password"
            :placeholder="selectedService === 'simplelogin' ? 'sl_...' : 'Paste your Bearer token...'"
            class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base font-mono text-sm"
          />
          <p class="text-xs text-grey-500 dark:text-grey-400 mt-2">
            <template v-if="selectedService === 'simplelogin'">
              Get this from SimpleLogin → Settings → API Keys.
            </template>
            <template v-else>
              Get this from Addy.io → Settings → API.
            </template>
            We never store the token — it's used only for this one import call.
          </p>
        </div>

        <!-- Step controls -->
        <div v-if="selectedService" class="mt-6 flex flex-wrap gap-3">
          <button
            @click="runDryRun"
            :disabled="!token || loading"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:cursor-not-allowed disabled:opacity-50"
          >
            {{ loading ? 'Checking…' : 'Preview import' }}
          </button>
          <button
            v-if="dryRunResult"
            @click="runImport"
            :disabled="loading || dryRunResult.importable === 0"
            class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-4 rounded disabled:cursor-not-allowed disabled:opacity-50"
          >
            {{ loading ? 'Importing…' : `Import ${dryRunResult.importable} aliases` }}
          </button>
        </div>

        <!-- Error -->
        <div
          v-if="errorMessage"
          class="mt-6 rounded-md border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4 text-sm text-red-700 dark:text-red-300"
        >
          {{ errorMessage }}
        </div>

        <!-- Dry run result -->
        <div
          v-if="dryRunResult && !importResult"
          class="mt-6 rounded-md border border-indigo-200 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-800 p-5"
        >
          <h4 class="font-semibold text-grey-900 dark:text-white mb-2">Preview</h4>
          <p class="text-sm text-grey-700 dark:text-grey-200">
            Found <b>{{ dryRunResult.total }}</b> aliases at the source.
            <b>{{ dryRunResult.importable }}</b> will fit in your current plan;
            <b>{{ dryRunResult.skipped }}</b> will be skipped.
          </p>
          <div v-if="dryRunResult.samples?.length" class="mt-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-grey-500 dark:text-grey-400 mb-2">
              Sample
            </p>
            <ul class="space-y-1">
              <li
                v-for="s in dryRunResult.samples"
                :key="s.email"
                class="text-xs font-mono text-grey-700 dark:text-grey-200 flex items-center gap-2"
              >
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide"
                  :class="s.active
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                    : 'bg-grey-200 text-grey-500 dark:bg-grey-700'"
                >{{ s.active ? 'Active' : 'Paused' }}</span>
                {{ s.email }}
                <span v-if="s.description" class="text-grey-500 dark:text-grey-400">— {{ s.description }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Import result -->
        <div
          v-if="importResult"
          class="mt-6 rounded-md border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 p-5"
        >
          <h4 class="font-semibold text-green-900 dark:text-green-200 mb-2">Done</h4>
          <p class="text-sm text-grey-700 dark:text-grey-200">
            Imported <b>{{ importResult.imported }}</b> aliases.
            <template v-if="importResult.skipped_over_limit > 0">
              <b>{{ importResult.skipped_over_limit }}</b> were skipped because they'd exceed your
              plan's alias limit.
            </template>
          </p>
          <div class="mt-3">
            <Link
              :href="route('aliases.index')"
              class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 font-semibold text-sm"
            >
              View imported aliases
            </Link>
          </div>
        </div>

        <!-- Firefox Relay note -->
        <div class="mt-12 pt-6 border-t border-grey-200 dark:border-grey-700">
          <h4 class="font-semibold text-grey-900 dark:text-white mb-2">Firefox Relay users</h4>
          <p class="text-sm text-grey-700 dark:text-grey-200">
            Firefox Relay doesn't ship a public user-facing API, so we can't automate the import.
            Export your aliases from Relay's settings, then
            <a href="https://mailflusher.com/contact" class="text-indigo-700 dark:text-indigo-400 underline">
              contact us
            </a>
            with the file — we'll do the import manually, free.
          </p>
        </div>
      </div>
    </div>
  </SettingsLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SettingsLayout from '@/Layouts/SettingsLayout.vue'

const page = usePage()

const services = [
  {
    slug: 'simplelogin',
    name: 'SimpleLogin',
    hint: 'Addresses at simplelogin.io / proton.me aliases',
    disabled: false,
  },
  {
    slug: 'addy',
    name: 'Addy.io',
    hint: 'Addresses at addy.io (or self-hosted Addy)',
    disabled: false,
  },
  {
    slug: 'firefox_relay',
    name: 'Firefox Relay',
    hint: 'No public API — see below for manual migration',
    disabled: true,
  },
]

const selectedService = ref(null)
const token = ref('')
const loading = ref(false)
const dryRunResult = ref(null)
const importResult = ref(null)
const errorMessage = ref('')

const defaultDomain = computed(() => {
  const user = page.props.user
  return user?.default_alias_domain || (user?.username ? `${user.username}.${page.props.appDomain || 'mailflusher.com'}` : 'your-subdomain')
})

const selectService = slug => {
  selectedService.value = slug
  dryRunResult.value = null
  importResult.value = null
  errorMessage.value = ''
}

const runDryRun = () => {
  loading.value = true
  errorMessage.value = ''
  importResult.value = null
  axios
    .post('/api/v1/import/dry-run', {
      service: selectedService.value,
      token: token.value,
    })
    .then(({ data }) => {
      dryRunResult.value = data
      loading.value = false
    })
    .catch(error => {
      loading.value = false
      errorMessage.value = error.response?.data?.error || error.response?.data?.message || 'Failed to preview.'
    })
}

const runImport = () => {
  loading.value = true
  errorMessage.value = ''
  axios
    .post('/api/v1/import', {
      service: selectedService.value,
      token: token.value,
    })
    .then(({ data }) => {
      importResult.value = data
      loading.value = false
      token.value = ''
    })
    .catch(error => {
      loading.value = false
      errorMessage.value = error.response?.data?.error || error.response?.data?.message || 'Failed to import.'
    })
}
</script>
