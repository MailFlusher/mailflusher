<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <!-- Paywall -->
      <div v-if="!$page.props.user?.can_use_ghost_inbox" class="pt-10">
        <div class="rounded-lg border border-indigo-200 bg-gradient-to-br from-indigo-50 to-cyan-50 dark:from-indigo-900/20 dark:to-cyan-900/20 dark:border-indigo-800 p-8 text-center">
          <h3 class="text-lg font-medium text-grey-900 dark:text-white mb-2">
            Ghost Inbox is a Pro feature
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200 mb-6 max-w-xl mx-auto">
            Aliases in ghost mode store incoming mail in a browser-only inbox that's encrypted with
            an OpenPGP key only your browser can unlock. Even our database operators can't read it.
          </p>
          <Link
            :href="route('subscription.index')"
            class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 font-semibold"
          >
            Upgrade to Pro
          </Link>
        </div>
      </div>

      <!-- Setup flow -->
      <div v-else-if="!vaultExists" class="pt-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
            Set up your Ghost Inbox vault
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            Your browser will generate an OpenPGP keypair. The public key is sent to us so we can
            encrypt incoming mail for ghost-mode aliases. The private key is encrypted with your
            passphrase and uploaded — so you can unlock it from any browser where you log in — but
            the passphrase itself never leaves this device.
          </p>
          <p class="text-amber-700 dark:text-amber-300 text-sm !mt-3">
            <b>Important:</b> if you forget the passphrase, stored emails are permanently
            unreadable. We can't recover it. You'll get a recovery sheet to download when you're
            done.
          </p>
        </div>

        <div class="mt-6 max-w-md space-y-4">
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Vault passphrase
            </label>
            <input
              v-model="setupForm.passphrase"
              type="password"
              placeholder="At least 12 characters"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Confirm passphrase
            </label>
            <input
              v-model="setupForm.confirmPassphrase"
              type="password"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Passphrase hint (optional — stored on the recovery sheet only)
            </label>
            <input
              v-model="setupForm.hint"
              type="text"
              placeholder="e.g. the usual one, with the comma"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <p v-if="setupError" class="text-sm text-red-600">{{ setupError }}</p>
          <button
            @click="doSetup"
            :disabled="setupLoading"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:opacity-50"
          >
            {{ setupLoading ? 'Generating keypair…' : 'Generate vault' }}
          </button>
        </div>
      </div>

      <!-- Vault exists — show status + settings -->
      <div v-else class="pt-10">
        <div class="rounded-md border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 p-5 mb-8">
          <h4 class="font-semibold text-green-900 dark:text-green-200 mb-1">Vault active</h4>
          <p class="text-sm text-grey-700 dark:text-grey-200">
            Created {{ formatDate(vault.vault_created_at) }}. Ghost-mode aliases now store mail
            encrypted with your public key.
          </p>
        </div>

        <div class="space-y-4 max-w-md">
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Auto-lock after (minutes)
            </label>
            <input
              v-model.number="settings.ghost_lock_minutes"
              type="number"
              min="1"
              max="1440"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
            <p class="text-xs text-grey-500 dark:text-grey-400 mt-1">
              How long the unlocked vault stays in memory without activity. Default 15.
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
              Preview mode
            </label>
            <select
              v-model="settings.ghost_preview_mode"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            >
              <option value="preview_10">Show first 10 chars of From and Subject</option>
              <option value="encrypted">Encrypt everything (no previews at all)</option>
            </select>
          </div>

          <button
            @click="saveSettings"
            :disabled="settingsLoading"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:opacity-50"
          >
            Save settings
          </button>
          <span v-if="settingsSaved" class="ml-3 text-sm text-green-600">Saved.</span>
        </div>

        <div class="mt-10 pt-6 border-t border-grey-200 dark:border-grey-700">
          <h4 class="font-semibold text-red-700 dark:text-red-400 mb-2">Destroy vault</h4>
          <p class="text-sm text-grey-700 dark:text-grey-200 mb-3">
            Deletes your vault keys and every stored email. Can't be undone. Your alias settings
            stay, but mail to ghost-mode aliases will be silently dropped until you set up a new
            vault.
          </p>
          <button
            @click="destroyOpen = true"
            class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-4 rounded"
          >
            Destroy vault
          </button>
        </div>
      </div>
    </div>

    <!-- Destroy confirm -->
    <Modal :open="destroyOpen" @close="destroyOpen = false">
      <template v-slot:title>Destroy vault?</template>
      <template v-slot:content>
        <p class="text-sm text-grey-700 dark:text-grey-200 mt-4">
          Every stored email will be permanently deleted and your keys rotated out. This can't be
          undone.
        </p>
        <div class="mt-6 flex gap-3">
          <button
            @click="doDestroy"
            class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-4 rounded"
          >
            Yes, destroy
          </button>
          <button
            @click="destroyOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <!-- Recovery sheet modal -->
    <Modal :open="!!recoveryContents" @close="dismissRecovery">
      <template v-slot:title>Save your recovery sheet</template>
      <template v-slot:content>
        <p class="text-sm text-grey-700 dark:text-grey-200 mt-4">
          This contains your armored public key and your passphrase-encrypted private key. Without
          it — plus the passphrase — your stored emails cannot be recovered. Save it somewhere safe
          (password manager, printed copy, encrypted cloud drive).
        </p>
        <div class="mt-6 flex gap-3">
          <button
            @click="downloadRecovery"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded"
          >
            Download recovery sheet
          </button>
          <button
            @click="dismissRecovery"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            I've saved it
          </button>
        </div>
      </template>
    </Modal>
  </SettingsLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import SettingsLayout from '@/Layouts/SettingsLayout.vue'
import Modal from '@/Components/Modal.vue'
import { generateVault, recoverySheet, downloadRecoverySheet } from '@/services/GhostVault.js'

const vaultExists = ref(false)
const vault = ref({})
const settings = reactive({ ghost_lock_minutes: 15, ghost_preview_mode: 'preview_10' })
const settingsLoading = ref(false)
const settingsSaved = ref(false)

const setupForm = reactive({ passphrase: '', confirmPassphrase: '', hint: '' })
const setupError = ref('')
const setupLoading = ref(false)

const destroyOpen = ref(false)
const recoveryContents = ref(null)

const loadVault = () => {
  axios
    .get('/api/v1/ghost-vault')
    .then(({ data }) => {
      vaultExists.value = data.has_vault
      vault.value = data
      settings.ghost_lock_minutes = data.ghost_lock_minutes || 15
      settings.ghost_preview_mode = data.ghost_preview_mode || 'preview_10'
    })
    .catch(() => {})
}

onMounted(loadVault)

const doSetup = async () => {
  setupError.value = ''
  if (setupForm.passphrase.length < 12) {
    setupError.value = 'Passphrase must be at least 12 characters.'
    return
  }
  if (setupForm.passphrase !== setupForm.confirmPassphrase) {
    setupError.value = 'Passphrases do not match.'
    return
  }
  setupLoading.value = true
  try {
    const { publicKey, privateKey } = await generateVault(setupForm.passphrase)
    const response = await axios.post('/api/v1/ghost-vault', {
      vault_public_key: publicKey,
      vault_encrypted_private_key: privateKey,
    })
    recoveryContents.value = recoverySheet({
      publicKey,
      encryptedPrivateKey: privateKey,
      createdAt: response.data.vault_created_at,
      passphraseHint: setupForm.hint,
    })
    setupForm.passphrase = ''
    setupForm.confirmPassphrase = ''
    loadVault()
  } catch (e) {
    setupError.value = e.response?.data?.error || e.message || 'Failed to generate vault.'
  }
  setupLoading.value = false
}

const saveSettings = () => {
  settingsLoading.value = true
  settingsSaved.value = false
  axios
    .patch('/api/v1/ghost-vault/settings', {
      ghost_lock_minutes: settings.ghost_lock_minutes,
      ghost_preview_mode: settings.ghost_preview_mode,
    })
    .then(() => {
      settingsSaved.value = true
      settingsLoading.value = false
      setTimeout(() => (settingsSaved.value = false), 3000)
    })
    .catch(() => {
      settingsLoading.value = false
    })
}

const doDestroy = () => {
  axios.delete('/api/v1/ghost-vault').then(() => {
    destroyOpen.value = false
    vaultExists.value = false
    vault.value = {}
  })
}

const dismissRecovery = () => {
  recoveryContents.value = null
}

const downloadRecovery = () => {
  if (recoveryContents.value) {
    downloadRecoverySheet(recoveryContents.value)
  }
}

const formatDate = ts => {
  if (!ts) return '—'
  return new Date(ts).toLocaleString()
}
</script>
