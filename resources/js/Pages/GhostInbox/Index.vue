<template>
  <div>
    <Head title="Ghost Inbox" />

    <!-- Paywall -->
    <div v-if="!canUseGhostInbox" class="max-w-3xl mx-auto">
      <div
        class="rounded-lg border border-indigo-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-8 text-center"
      >
        <h1 class="text-2xl font-bold text-grey-900 mb-2">Ghost Inbox is a Pro feature</h1>
        <p class="text-grey-700 mb-6">
          Put any alias in "ghost mode" and incoming mail is encrypted with your vault key and
          stored in-browser only. It never forwards to your real inbox.
        </p>
        <Link
          :href="route('subscription.index')"
          class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 font-semibold"
        >
          Upgrade to Pro
        </Link>
      </div>
    </div>

    <!-- Vault not set up -->
    <div v-else-if="!hasGhostVault" class="max-w-3xl mx-auto">
      <div class="rounded-lg border border-amber-200 bg-amber-50 p-8 text-center">
        <h1 class="text-2xl font-bold text-grey-900 mb-2">Set up your vault first</h1>
        <p class="text-grey-700 mb-6">
          You need to generate a Ghost Inbox vault before you can store or read ghost-mode emails.
        </p>
        <Link
          :href="route('settings.ghost_inbox')"
          class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 font-semibold"
        >
          Set up vault
        </Link>
      </div>
    </div>

    <!-- Main UI -->
    <div v-else class="max-w-5xl mx-auto">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-grey-900 dark:text-white">Ghost Inbox</h1>
          <p class="text-sm text-grey-500 dark:text-grey-400">
            Encrypted, browser-only mail storage. Even we can't read this.
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span
            v-if="!unlocked"
            class="inline-flex items-center rounded-full bg-grey-100 dark:bg-grey-800 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-grey-600 dark:text-grey-300"
            >Locked</span
          >
          <button
            v-if="unlocked"
            @click="lockNow"
            class="inline-flex items-center gap-2 rounded-md border border-grey-200 dark:border-grey-700 px-3 py-1.5 text-sm font-medium text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-800"
          >
            Lock now
          </button>
        </div>
      </div>

      <!-- Inbox (always visible; the list shows previews even when locked) -->
      <div>
        <div v-if="emails.length === 0" class="text-center py-16 text-grey-500 dark:text-grey-400">
          No stored emails yet. Set an alias to ghost mode and mail sent to it will appear here.
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <!-- List — on mobile, hide when an email is selected -->
          <div
            :class="[
              'lg:col-span-1 space-y-2 max-h-[70vh] overflow-y-auto',
              selectedEmail ? 'hidden lg:block' : '',
            ]"
          >
            <button
              v-for="email in emails"
              :key="email.id"
              @click="openEmail(email)"
              :class="[
                'w-full text-left p-3 rounded-lg border transition-colors',
                selectedEmail?.id === email.id
                  ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20'
                  : 'border-grey-200 dark:border-grey-700 bg-white dark:bg-grey-900 hover:border-indigo-200',
              ]"
            >
              <p class="text-sm font-semibold text-grey-900 dark:text-white truncate">
                <template v-if="fullHeaders[email.id]?.from">{{
                  fullHeaders[email.id].from
                }}</template>
                <template v-else-if="email.from_preview"
                  >{{ email.from_preview }}<span class="text-grey-400">…</span></template
                >
                <template v-else>Encrypted sender</template>
              </p>
              <p class="text-xs text-grey-500 dark:text-grey-400 truncate">
                <template v-if="fullHeaders[email.id]?.subject">{{
                  fullHeaders[email.id].subject
                }}</template>
                <template v-else-if="email.subject_preview"
                  >{{ email.subject_preview }}<span class="text-grey-400">…</span></template
                >
                <template v-else>Encrypted subject</template>
              </p>
              <p class="text-[11px] text-grey-400 dark:text-grey-500 mt-1">
                {{ formatDate(email.received_at) }} · {{ formatBytes(email.size_bytes) }}
              </p>
            </button>
          </div>

          <!-- Viewer -->
          <div :class="['lg:col-span-2', selectedEmail ? '' : 'hidden lg:block']">
            <div v-if="!selectedEmail" class="text-center py-16 text-grey-500 dark:text-grey-400">
              Pick an email to decrypt and read.
            </div>
            <div
              v-else
              class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 p-0 overflow-hidden"
            >
              <div
                class="px-4 py-3 border-b border-grey-100 dark:border-grey-700 flex items-center justify-between gap-2"
              >
                <button
                  @click="closeEmail"
                  class="lg:hidden -ml-2 p-1 text-grey-500 hover:text-grey-900"
                  aria-label="Back"
                >
                  <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15.75 19.5L8.25 12l7.5-7.5"
                    />
                  </svg>
                </button>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-semibold text-grey-900 dark:text-white truncate">
                    <template v-if="fullHeaders[selectedEmail.id]?.subject">{{
                      fullHeaders[selectedEmail.id].subject
                    }}</template>
                    <template v-else-if="selectedEmail.subject_preview"
                      >{{ selectedEmail.subject_preview
                      }}<span class="text-grey-400">…</span></template
                    >
                    <template v-else>Encrypted subject</template>
                  </p>
                  <p class="text-xs text-grey-500 dark:text-grey-400 truncate">
                    <template v-if="fullHeaders[selectedEmail.id]?.from">{{
                      fullHeaders[selectedEmail.id].from
                    }}</template>
                    <template v-else-if="selectedEmail.from_preview"
                      >{{ selectedEmail.from_preview
                      }}<span class="text-grey-400">…</span></template
                    >
                    <template v-else>Encrypted sender</template>
                    · {{ formatDate(selectedEmail.received_at) }}
                  </p>
                </div>
                <button
                  @click="deleteEmail(selectedEmail)"
                  class="text-xs text-red-600 hover:text-red-500 font-medium shrink-0"
                >
                  Delete
                </button>
              </div>
              <div v-if="!unlocked" class="p-6">
                <h3 class="text-sm font-semibold text-grey-900 dark:text-white mb-1">
                  Unlock vault to read
                </h3>
                <p class="text-xs text-grey-500 dark:text-grey-400 mb-4">
                  Enter your passphrase to decrypt this email. The passphrase never leaves this
                  device.
                </p>
                <input
                  v-model="passphrase"
                  @keyup.enter="doUnlock"
                  type="password"
                  placeholder="Vault passphrase"
                  autofocus
                  class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
                />
                <p v-if="unlockError" class="text-sm text-red-600 mt-2">{{ unlockError }}</p>
                <button
                  @click="doUnlock"
                  :disabled="unlockLoading || !passphrase"
                  class="mt-4 w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:opacity-50"
                >
                  {{ unlockLoading ? 'Unlocking…' : 'Unlock' }}
                </button>
              </div>
              <div v-else-if="decryptError" class="p-4 text-sm text-red-600">
                {{ decryptError }}
              </div>
              <div v-else-if="decryptLoading" class="p-4 text-sm text-grey-500">Decrypting…</div>
              <template v-else-if="decrypted">
                <div
                  v-if="decryptedText"
                  class="px-4 py-2 border-b border-grey-100 dark:border-grey-700 flex items-center gap-2 text-xs"
                >
                  <span class="text-grey-500 dark:text-grey-400">View:</span>
                  <button
                    @click="viewMode = 'html'"
                    :class="[
                      'rounded px-2 py-0.5 font-medium',
                      viewMode === 'html'
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                        : 'text-grey-600 dark:text-grey-300 hover:bg-grey-50 dark:hover:bg-grey-800',
                    ]"
                  >
                    HTML
                  </button>
                  <button
                    @click="viewMode = 'text'"
                    :class="[
                      'rounded px-2 py-0.5 font-medium',
                      viewMode === 'text'
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                        : 'text-grey-600 dark:text-grey-300 hover:bg-grey-50 dark:hover:bg-grey-800',
                    ]"
                  >
                    Plain text
                  </button>
                  <span class="ml-auto text-grey-400 dark:text-grey-500">
                    External images &amp; scripts blocked for privacy
                  </span>
                </div>
                <iframe
                  sandbox=""
                  :srcdoc="viewMode === 'text' ? textIframeSrc : decrypted"
                  class="w-full min-h-[55vh] border-0 bg-white"
                ></iframe>
                <div
                  v-if="attachments.length > 0"
                  class="px-4 py-3 border-t border-grey-100 dark:border-grey-700 bg-grey-50 dark:bg-grey-800"
                >
                  <p
                    class="text-xs font-semibold uppercase tracking-wide text-grey-500 dark:text-grey-400 mb-2"
                  >
                    Attachments ({{ attachments.length }})
                  </p>
                  <ul class="space-y-1">
                    <li
                      v-for="a in attachments"
                      :key="a.filename"
                      class="flex items-center justify-between gap-3 text-sm"
                    >
                      <span class="truncate text-grey-700 dark:text-grey-200">
                        <span class="font-mono text-xs text-grey-500">{{ a.contentType }}</span>
                        {{ a.filename }}
                      </span>
                      <button
                        @click="downloadAttachment(a)"
                        class="text-xs font-medium px-3 py-1 rounded border border-grey-200 dark:border-grey-600 text-grey-700 dark:text-grey-200 hover:bg-white dark:hover:bg-grey-700"
                      >
                        Download ({{ formatBytes(a.size) }})
                      </button>
                    </li>
                  </ul>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { unlockPrivateKey, decryptMessage } from '@/services/GhostVault.js'
import * as Session from '@/services/GhostVaultSession.js'

const props = defineProps({
  canUseGhostInbox: { type: Boolean, default: false },
  hasGhostVault: { type: Boolean, default: false },
})

const unlocked = ref(false)
const passphrase = ref('')
const unlockError = ref('')
const unlockLoading = ref(false)

const emails = ref([])
const selectedEmail = ref(null)
const decrypted = ref(null)
const decryptedText = ref(null)
const viewMode = ref('html') // 'html' | 'text'
const attachments = ref([])
const decryptError = ref('')
const decryptLoading = ref(false)

/**
 * Lazily-populated cache of { [emailId]: { from, subject } } once a message
 * is decrypted. The list and viewer show full header values from here when
 * present, falling back to the server-stored 10-char previews otherwise.
 */
const fullHeaders = ref({})

/** Extract the top-level From/Subject from a raw MIME string (header block only). */
const extractHeaders = mime => {
  const sepCrlf = mime.indexOf('\r\n\r\n')
  const sepLf = mime.indexOf('\n\n')
  const sep = sepCrlf !== -1 ? sepCrlf : sepLf
  const headers = sep === -1 ? mime : mime.slice(0, sep)

  // Unfold header continuations (RFC 5322 §2.2.3: lines starting with WSP
  // are continuations of the previous header).
  const unfolded = headers.replace(/\r?\n[\t ]+/g, ' ')
  const from = /^from:\s*(.+)$/im.exec(unfolded)?.[1]?.trim() || null
  const subject = /^subject:\s*(.+)$/im.exec(unfolded)?.[1]?.trim() || null
  return { from, subject }
}

Session.setOnLock(() => {
  unlocked.value = false
  selectedEmail.value = null
  decrypted.value = null
  decryptedText.value = null
  attachments.value = []
  // Full headers were derived from decrypted content; drop them on lock so
  // the list reverts to showing only the 10-char previews the server stores.
  fullHeaders.value = {}
})

const doUnlock = async () => {
  if (!passphrase.value) return
  unlockError.value = ''
  unlockLoading.value = true
  try {
    const vaultResp = await axios.get('/api/v1/ghost-vault')
    const key = await unlockPrivateKey(vaultResp.data.vault_encrypted_private_key, passphrase.value)
    Session.unlock(key, vaultResp.data.ghost_lock_minutes || 15)
    passphrase.value = ''
    unlocked.value = true

    // If the user clicked a message while locked, fulfil that click now.
    if (selectedEmail.value) {
      openEmail(selectedEmail.value)
    }

    // Opportunistically decrypt the rest in the background to populate full
    // From/Subject in the list. Best-effort — individual failures don't block.
    backfillHeaders()
  } catch (e) {
    unlockError.value = e.message || 'Unable to unlock.'
  }
  unlockLoading.value = false
}

/**
 * Decrypt every stored email (except the one already open) just far enough to
 * read its From and Subject headers, then drop the plaintext. Cheap enough
 * for typical 30-day retention inboxes; runs in parallel.
 */
const backfillHeaders = async () => {
  const key = Session.getKey()
  if (!key) return
  const pending = emails.value.filter(
    e => !fullHeaders.value[e.id] && e.id !== selectedEmail.value?.id,
  )

  await Promise.all(
    pending.map(async email => {
      try {
        const { data } = await axios.get(`/api/v1/ghost-emails/${email.id}`)
        const plaintext = await decryptMessage(data.encrypted_payload, key)
        fullHeaders.value = {
          ...fullHeaders.value,
          [email.id]: extractHeaders(plaintext),
        }
      } catch {
        // Ignore individual decryption failures — the row falls back to previews.
      }
    }),
  )
}

const lockNow = () => {
  Session.lock()
}

const loadEmails = () => {
  axios.get('/api/v1/ghost-emails').then(({ data }) => {
    emails.value = data.data
  })
}

const openEmail = async email => {
  selectedEmail.value = email
  decrypted.value = null
  decryptedText.value = null
  attachments.value = []
  decryptError.value = ''
  viewMode.value = 'html'
  Session.recordActivity()

  // If the vault is still locked, just surface the inline unlock prompt —
  // the template handles this via `v-if="!unlocked"` in the viewer body.
  if (!Session.isUnlocked()) {
    unlocked.value = false
    return
  }

  decryptLoading.value = true
  try {
    const key = Session.getKey()
    const { data } = await axios.get(`/api/v1/ghost-emails/${email.id}`)
    const plaintextMime = await decryptMessage(data.encrypted_payload, key)

    // Cache full headers so the list + viewer can show the real From/Subject.
    fullHeaders.value = {
      ...fullHeaders.value,
      [email.id]: extractHeaders(plaintextMime),
    }

    let rendered
    try {
      rendered = renderMimeAsHtml(plaintextMime)
    } catch (parseErr) {
      // If MIME parsing itself blows up, fall back to raw
      rendered = {
        html: rawFallbackHtml(plaintextMime),
        text: plaintextMime,
        attachments: [],
      }
    }

    // If the HTML body came back empty (email was text-only, or all parts were
    // attachments), synthesize a plaintext view so the user still sees something.
    if (!rendered.html || isEmptyDocument(rendered.html)) {
      rendered.html = rawFallbackHtml(rendered.text || plaintextMime)
    }

    decrypted.value = rendered.html
    decryptedText.value = rendered.text || plaintextMime
    attachments.value = rendered.attachments || []
  } catch (e) {
    decryptError.value = e.message || 'Decryption failed.'
  }yes
  decryptLoading.value = false
}

const isEmptyDocument = html => {
  // Heuristic: parse the HTML and inspect visible text content from <body>.
  // Avoid regex-based multi-character sanitization when stripping tags.
  const source = html || ''
  const doc = new DOMParser().parseFromString(source, 'text/html')
  const text = doc.body && doc.body.textContent ? doc.body.textContent : ''
  return text.replace(/\s+/g, '').length === 0
}

const textIframeSrc = computed(() => rawFallbackHtml(decryptedText.value || ''))

const rawFallbackHtml = raw => {
  // Strip the top-level MIME headers if present so the user sees the body, not
  // the envelope. Otherwise just dump the raw string.
  let body = raw || ''
  const sep =
    body.indexOf('\r\n\r\n') !== -1
      ? body.indexOf('\r\n\r\n') + 4
      : body.indexOf('\n\n') !== -1
        ? body.indexOf('\n\n') + 2
        : 0
  body = body.slice(sep)
  const escaped = body.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  return `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="default-src 'none'; style-src 'unsafe-inline';">
<style>body { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; margin: 1.5rem; white-space: pre-wrap; color: #111; background: #fff; }</style>
</head>
<body>${escaped}</body>
</html>`
}

const closeEmail = () => {
  selectedEmail.value = null
  decrypted.value = null
  attachments.value = []
}

const downloadAttachment = a => {
  try {
    // Decode base64 payload to binary then offer as blob
    const binary = atob(a.contentBase64)
    const bytes = new Uint8Array(binary.length)
    for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i)
    const blob = new Blob([bytes], { type: a.contentType || 'application/octet-stream' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = a.filename || 'attachment'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    setTimeout(() => URL.revokeObjectURL(url), 1000)
  } catch (e) {
    alert('Could not decode attachment: ' + e.message)
  }
}

const deleteEmail = email => {
  if (!confirm('Delete this stored email? This cannot be undone.')) return
  axios.delete(`/api/v1/ghost-emails/${email.id}`).then(() => {
    emails.value = emails.value.filter(e => e.id !== email.id)
    if (selectedEmail.value?.id === email.id) {
      selectedEmail.value = null
      decrypted.value = null
    }
  })
}

/**
 * Very conservative MIME → { html, attachments[] } rendering. We grab the
 * text/html (or text/plain) part for display, and collect any attachment
 * parts (anything with Content-Disposition: attachment or with a filename).
 * Rendered HTML is sandboxed in the iframe caller.
 */
const renderMimeAsHtml = raw => {
  const parsed = parseMimePart(raw)
  const { htmlBody, textBody, attachments: atts } = collectParts(parsed)

  const chosen = htmlBody || ''
  let html
  if (chosen) {
    html = `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="default-src 'none'; style-src 'unsafe-inline'; img-src data:;">
<style>body { font-family: system-ui, sans-serif; font-size: 14px; margin: 1.5rem; color: #111; background: #fff; }</style>
</head>
<body>${chosen}</body>
</html>`
  } else {
    const escaped = (textBody || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
    html = `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="default-src 'none'; style-src 'unsafe-inline';">
<style>body { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; margin: 1.5rem; white-space: pre-wrap; color: #111; background: #fff; }</style>
</head>
<body>${escaped}</body>
</html>`
  }

  return { html, text: textBody, attachments: atts }
}

const parseMimePart = raw => {
  const sepIdx = raw.indexOf('\r\n\r\n') !== -1 ? raw.indexOf('\r\n\r\n') : raw.indexOf('\n\n')
  const sepLen = raw.indexOf('\r\n\r\n') !== -1 ? 4 : 2
  const headers = raw.slice(0, sepIdx === -1 ? 0 : sepIdx)
  const body = sepIdx === -1 ? raw : raw.slice(sepIdx + sepLen)

  const contentType = (/content-type:\s*([^\r\n;]+)/i.exec(headers)?.[1] || 'text/plain')
    .toLowerCase()
    .trim()
  const boundary = /boundary="?([^"\r\n;]+)"?/i.exec(headers)?.[1] || null
  const disposition = /content-disposition:\s*([^\r\n;]+)/i.exec(headers)?.[1]?.toLowerCase().trim()
  const filenameMatch = /filename="?([^"\r\n;]+)"?/i.exec(headers)
  const filename = filenameMatch ? filenameMatch[1] : null
  const encoding =
    /content-transfer-encoding:\s*([^\r\n]+)/i.exec(headers)?.[1]?.toLowerCase().trim() || '7bit'

  return { contentType, boundary, disposition, filename, encoding, headers, body }
}

const collectParts = part => {
  let htmlBody = ''
  let textBody = ''
  const attachments = []

  const walk = p => {
    if (p.boundary && p.contentType.startsWith('multipart/')) {
      const parts = p.body.split(`--${p.boundary}`)
      for (const sub of parts) {
        const trimmed = sub.trim()
        if (!trimmed || trimmed === '--') continue
        walk(parseMimePart(trimmed))
      }
      return
    }

    const isAttachment =
      p.disposition === 'attachment' || (p.filename && !p.contentType.startsWith('text/'))

    if (isAttachment && p.filename) {
      // Normalize encoding to base64 for the download helper
      let b64 = ''
      try {
        if (p.encoding === 'base64') {
          b64 = p.body.replace(/\s+/g, '')
        } else if (p.encoding === 'quoted-printable') {
          // Convert QP to raw then base64
          const raw = decodeQuotedPrintable(p.body)
          b64 = btoa(unescape(encodeURIComponent(raw)))
        } else {
          b64 = btoa(unescape(encodeURIComponent(p.body)))
        }
      } catch (e) {
        b64 = ''
      }

      attachments.push({
        filename: p.filename,
        contentType: p.contentType,
        size: Math.round((b64.length * 3) / 4),
        contentBase64: b64,
      })
      return
    }

    if (p.contentType.includes('text/html') && !htmlBody) {
      htmlBody = decodeBodyText(p.body, p.encoding)
    } else if (p.contentType.includes('text/plain') && !textBody) {
      textBody = decodeBodyText(p.body, p.encoding)
    }
  }

  walk(part)
  return { htmlBody, textBody, attachments }
}

const decodeBodyText = (body, encoding) => {
  if (encoding === 'base64') {
    try {
      return decodeURIComponent(escape(atob(body.replace(/\s+/g, ''))))
    } catch {
      return body
    }
  }
  if (encoding === 'quoted-printable') {
    return decodeQuotedPrintable(body)
  }
  return body
}

const decodeQuotedPrintable = s => {
  return s
    .replace(/=\r?\n/g, '')
    .replace(/=([A-Fa-f0-9]{2})/g, (_, hex) => String.fromCharCode(parseInt(hex, 16)))
}

const onActivity = () => Session.recordActivity()

onMounted(() => {
  if (!props.canUseGhostInbox || !props.hasGhostVault) return
  unlocked.value = Session.isUnlocked()
  // Always load the list — it only contains preview fields the server
  // already knows. Unlock is only required to decrypt individual messages.
  loadEmails()
  // If the vault is already unlocked (e.g. coming back from Settings), start
  // filling in the full headers for the list.
  if (unlocked.value) backfillHeaders()
  window.addEventListener('pointerdown', onActivity)
  window.addEventListener('keydown', onActivity)
})

onBeforeUnmount(() => {
  window.removeEventListener('pointerdown', onActivity)
  window.removeEventListener('keydown', onActivity)
})

const formatDate = ts => (ts ? new Date(ts).toLocaleString() : '—')
const formatBytes = n => {
  if (!n || n < 1024) return `${n || 0} B`
  if (n < 1024 * 1024) return `${Math.round(n / 1024)} KB`
  return `${(n / (1024 * 1024)).toFixed(1)} MB`
}
</script>
