<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <!-- Paywall for free users -->
      <div v-if="!$page.props.user?.can_use_webhooks" class="pt-10">
        <div class="rounded-lg border border-indigo-200 bg-gradient-to-br from-indigo-50 to-cyan-50 dark:from-indigo-900/20 dark:to-cyan-900/20 dark:border-indigo-800 p-8 text-center">
          <h3 class="text-lg font-medium text-grey-900 dark:text-white mb-2">
            Webhooks are a Standard feature
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200 mb-6 max-w-xl mx-auto">
            Subscribe to <code>alias.received</code>, <code>alias.blocked</code>, and
            <code>alias.leaked</code> events. Every delivery is HMAC-SHA256 signed with a per-webhook
            secret, retried with exponential backoff, and logged for audit.
          </p>
          <Link
            :href="route('subscription.index')"
            class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 font-semibold"
          >
            Upgrade to unlock webhooks
          </Link>
        </div>
      </div>

      <!-- Standard+ UI -->
      <template v-else>
        <div class="pt-10">
          <div class="space-y-1">
            <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
              Outbound Webhooks
            </h3>
            <p class="text-base text-grey-700 dark:text-grey-200">
              Get an HTTP POST to your URL whenever an alias receives, blocks, or is flagged for a
              leak. Payloads are JSON and signed with HMAC-SHA256 in the
              <code>X-MailFlusher-Signature</code> header. Failed deliveries retry up to 5 times
              with exponential backoff.
            </p>
          </div>

          <div class="mt-6">
            <button
              @click="openCreateModal"
              class="bg-indigo-600 w-full hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
              Create New Webhook
            </button>
          </div>

          <div class="mt-8">
            <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
              Your Webhooks
            </h3>
            <div class="my-4 w-24 border-b-2 border-grey-200"></div>

            <p
              v-if="webhooks.length === 0"
              class="text-base text-grey-700 dark:text-grey-200"
            >
              You have not created any webhooks yet.
            </p>

            <div v-else class="space-y-3">
              <div
                v-for="webhook in webhooks"
                :key="webhook.id"
                class="border border-grey-200 dark:border-grey-700 rounded-lg p-4 bg-white dark:bg-grey-800"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                      <span
                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                        :class="webhook.active
                          ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                          : 'bg-grey-100 text-grey-500 dark:bg-grey-700 dark:text-grey-400'"
                      >
                        {{ webhook.active ? 'Active' : 'Paused' }}
                      </span>
                      <span
                        v-for="ev in webhook.events"
                        :key="ev"
                        class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 px-2 py-0.5 text-[10px] font-mono text-indigo-700 dark:text-indigo-300"
                      >
                        {{ ev }}
                      </span>
                    </div>
                    <p class="font-mono text-sm text-grey-900 dark:text-white break-all">
                      {{ webhook.url }}
                    </p>
                    <p
                      v-if="webhook.description"
                      class="text-xs text-grey-500 dark:text-grey-400 mt-1"
                    >
                      {{ webhook.description }}
                    </p>
                    <p class="text-xs text-grey-500 dark:text-grey-400 mt-1">
                      <template v-if="webhook.last_delivered_at">
                        Last delivery
                        <span
                          :class="webhook.last_response_code && webhook.last_response_code < 400 ? 'text-green-600' : 'text-red-500'"
                        >{{ webhook.last_response_code || '—' }}</span>
                        · {{ formatDate(webhook.last_delivered_at) }}
                      </template>
                      <template v-else>Never delivered</template>
                    </p>
                  </div>
                  <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                    <button
                      @click="viewDeliveries(webhook)"
                      class="text-xs font-medium px-3 py-1.5 rounded border border-grey-200 dark:border-grey-600 text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-700"
                    >
                      Deliveries
                    </button>
                    <button
                      @click="toggleActive(webhook)"
                      class="text-xs font-medium px-3 py-1.5 rounded border border-grey-200 dark:border-grey-600 text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-700"
                    >
                      {{ webhook.active ? 'Pause' : 'Resume' }}
                    </button>
                    <button
                      @click="confirmDelete(webhook)"
                      class="text-xs font-medium px-3 py-1.5 rounded border border-red-200 text-red-600 hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-900/20"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Create modal -->
    <Modal :open="createModalOpen" @close="createModalOpen = false">
      <template v-slot:title>Create Webhook</template>
      <template v-slot:content>
        <div class="space-y-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              URL (HTTPS only)
            </label>
            <input
              v-model="createForm.url"
              type="url"
              placeholder="https://example.com/webhook"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Description (optional)
            </label>
            <input
              v-model="createForm.description"
              type="text"
              placeholder="What this webhook is for..."
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-2">
              Events
            </label>
            <label
              v-for="ev in availableEvents"
              :key="ev.value"
              class="flex items-start gap-2 mb-2"
            >
              <input
                type="checkbox"
                :value="ev.value"
                v-model="createForm.events"
                class="mt-1 rounded text-indigo-600 focus:ring-indigo-500"
              />
              <span class="text-sm text-grey-700 dark:text-grey-200">
                <code class="font-mono text-indigo-700 dark:text-indigo-400">{{ ev.value }}</code>
                — {{ ev.description }}
              </span>
            </label>
          </div>
          <p v-if="createError" class="text-sm text-red-600">{{ createError }}</p>
        </div>
        <div class="mt-6 flex gap-3">
          <button
            @click="submitCreate"
            :disabled="createLoading"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:cursor-not-allowed"
          >
            Create
          </button>
          <button
            @click="createModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <!-- Secret display modal (shown once after create) -->
    <Modal :open="!!createdSecret" @close="dismissSecret">
      <template v-slot:title>Save this signing secret</template>
      <template v-slot:content>
        <p class="text-sm text-grey-700 dark:text-grey-200 mt-4">
          This is the <strong>only time</strong> we show the secret. Copy it now and store it
          somewhere safe. Use it to verify the <code>X-MailFlusher-Signature</code> HMAC on every
          incoming delivery.
        </p>
        <div class="mt-4 bg-grey-100 dark:bg-grey-800 rounded-md p-3 font-mono text-sm break-all text-grey-900 dark:text-white">
          {{ createdSecret }}
        </div>
        <div class="mt-6 flex gap-3">
          <button
            @click="copySecret"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded"
          >
            Copy to clipboard
          </button>
          <button
            @click="dismissSecret"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            I saved it
          </button>
        </div>
      </template>
    </Modal>

    <!-- Delete confirm modal -->
    <Modal :open="!!webhookToDelete" @close="webhookToDelete = null">
      <template v-slot:title>Delete Webhook?</template>
      <template v-slot:content>
        <p class="text-sm text-grey-700 dark:text-grey-200 mt-4">
          This will stop all future deliveries to
          <code class="font-mono break-all">{{ webhookToDelete?.url }}</code>. It cannot be undone.
        </p>
        <div class="mt-6 flex gap-3">
          <button
            @click="doDelete"
            class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-4 rounded"
          >
            Delete
          </button>
          <button
            @click="webhookToDelete = null"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <!-- Deliveries modal -->
    <Modal :open="!!deliveriesWebhook" @close="deliveriesWebhook = null">
      <template v-slot:title>
        Recent deliveries
      </template>
      <template v-slot:content>
        <p class="text-xs font-mono text-grey-500 dark:text-grey-400 mt-2 break-all">
          {{ deliveriesWebhook?.url }}
        </p>
        <div class="mt-4">
          <p
            v-if="deliveriesLoading"
            class="text-sm text-grey-500 dark:text-grey-400"
          >Loading…</p>
          <p
            v-else-if="deliveries.length === 0"
            class="text-sm text-grey-500 dark:text-grey-400"
          >No deliveries yet.</p>
          <div v-else class="space-y-2 max-h-96 overflow-y-auto">
            <div
              v-for="d in deliveries"
              :key="d.id"
              class="flex items-center justify-between gap-3 text-xs border-b border-grey-100 dark:border-grey-700 pb-2"
            >
              <span class="font-mono text-indigo-700 dark:text-indigo-400">{{ d.event }}</span>
              <span
                :class="statusColour(d)"
                class="inline-flex items-center rounded-full px-2 py-0.5 font-bold uppercase tracking-wide"
              >{{ d.status }}{{ d.response_code ? ` · ${d.response_code}` : '' }}</span>
              <span class="text-grey-500 dark:text-grey-400">
                {{ formatDate(d.delivered_at || d.created_at) }}
              </span>
              <span class="text-grey-400">attempt {{ d.attempts }}</span>
            </div>
          </div>
        </div>
        <div class="mt-6">
          <button
            @click="deliveriesWebhook = null"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
  </SettingsLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import SettingsLayout from '@/Layouts/SettingsLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  initialWebhooks: {
    type: Array,
    default: () => [],
  },
})

const webhooks = ref([...props.initialWebhooks])
const createModalOpen = ref(false)
const createLoading = ref(false)
const createError = ref('')
const createdSecret = ref(null)
const webhookToDelete = ref(null)
const deliveriesWebhook = ref(null)
const deliveries = ref([])
const deliveriesLoading = ref(false)

const availableEvents = [
  { value: 'alias.received', description: 'An alias forwarded an email to a recipient.' },
  { value: 'alias.blocked', description: 'A user rule blocked a forward.' },
  { value: 'alias.leaked', description: 'A new suspected leak event was created.' },
]

const createForm = ref({
  url: '',
  description: '',
  events: [],
})

const openCreateModal = () => {
  createForm.value = { url: '', description: '', events: [] }
  createError.value = ''
  createModalOpen.value = true
}

const submitCreate = () => {
  if (!createForm.value.url) {
    createError.value = 'URL is required.'
    return
  }
  if (createForm.value.events.length === 0) {
    createError.value = 'Pick at least one event.'
    return
  }
  createLoading.value = true
  axios
    .post('/api/v1/webhooks', createForm.value)
    .then(({ data }) => {
      webhooks.value = [data.data, ...webhooks.value]
      createdSecret.value = data.secret
      createModalOpen.value = false
      createLoading.value = false
    })
    .catch(error => {
      createLoading.value = false
      if (error.response?.status === 422) {
        const errors = error.response.data.errors || {}
        createError.value = Object.values(errors).flat().join(' ')
      } else if (error.response?.status === 403) {
        createError.value = 'Webhooks require a Standard or Pro plan.'
      } else {
        createError.value = 'Unexpected error creating webhook.'
      }
    })
}

const dismissSecret = () => {
  createdSecret.value = null
}

const copySecret = () => {
  if (!createdSecret.value) return
  navigator.clipboard?.writeText(createdSecret.value)
}

const toggleActive = webhook => {
  axios
    .patch(`/api/v1/webhooks/${webhook.id}`, { active: !webhook.active })
    .then(({ data }) => {
      const idx = webhooks.value.findIndex(w => w.id === webhook.id)
      if (idx !== -1) webhooks.value[idx] = data.data
    })
}

const confirmDelete = webhook => {
  webhookToDelete.value = webhook
}

const doDelete = () => {
  if (!webhookToDelete.value) return
  const id = webhookToDelete.value.id
  axios.delete(`/api/v1/webhooks/${id}`).then(() => {
    webhooks.value = webhooks.value.filter(w => w.id !== id)
    webhookToDelete.value = null
  })
}

const viewDeliveries = webhook => {
  deliveriesWebhook.value = webhook
  deliveries.value = []
  deliveriesLoading.value = true
  axios
    .get(`/api/v1/webhooks/${webhook.id}/deliveries`)
    .then(({ data }) => {
      deliveries.value = data.data
      deliveriesLoading.value = false
    })
    .catch(() => {
      deliveriesLoading.value = false
    })
}

const statusColour = d => {
  if (d.status === 'success') return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
  if (d.status === 'failed') return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
  if (d.status === 'giving_up') return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
  return 'bg-grey-100 text-grey-700 dark:bg-grey-800 dark:text-grey-300'
}

const formatDate = ts => {
  if (!ts) return '—'
  const d = new Date(ts)
  return d.toLocaleString()
}
</script>
