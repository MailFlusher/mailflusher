/**
 * Client-side OpenPGP wrapper for MailFlusher's Ghost Inbox.
 *
 * The private key never leaves the browser in plaintext. Server stores only:
 *   - public key (armored)
 *   - private key armored + passphrase-protected (what OpenPGP.js emits from
 *     generateKey({passphrase}))
 *
 * On read, we fetch the encrypted private key from the server, decrypt it
 * locally with the user's passphrase, and keep the decrypted PrivateKey in
 * memory for the current session (cleared on lock timeout).
 */

// Dynamic import keeps the ~200 kB OpenPGP.js out of the main bundle
let openpgpPromise = null
const openpgp = () => {
  if (!openpgpPromise) {
    openpgpPromise = import('openpgp')
  }
  return openpgpPromise
}

/**
 * Generate a new vault keypair. Returns the armored public key and the
 * passphrase-encrypted armored private key. Both get POSTed to /api/v1/ghost-vault.
 */
export async function generateVault(passphrase, userIdentifier = 'mailflusher-vault') {
  const pgp = await openpgp()
  const { privateKey, publicKey } = await pgp.generateKey({
    type: 'ecc',
    curve: 'curve25519',
    userIDs: [{ name: userIdentifier, email: `${userIdentifier}@mailflusher.local` }],
    passphrase,
    format: 'armored',
  })
  return { publicKey, privateKey }
}

/**
 * Unlock a passphrase-encrypted private key. Returns a live PrivateKey
 * object that can decrypt ciphertexts until cleared.
 */
export async function unlockPrivateKey(encryptedPrivateKeyArmored, passphrase) {
  const pgp = await openpgp()
  const encryptedKey = await pgp.readPrivateKey({ armoredKey: encryptedPrivateKeyArmored })
  try {
    return await pgp.decryptKey({ privateKey: encryptedKey, passphrase })
  } catch (e) {
    throw new Error('Incorrect passphrase or corrupt vault key.')
  }
}

export async function decryptMessage(ciphertextArmored, privateKey) {
  const pgp = await openpgp()
  const message = await pgp.readMessage({ armoredMessage: ciphertextArmored })
  const { data } = await pgp.decrypt({ message, decryptionKeys: privateKey, format: 'utf8' })
  return data
}

/**
 * Produce a printable recovery sheet (plain text). The user should save this
 * somewhere safe — it's the only way to recover data if they forget the
 * passphrase but we still have the encrypted private key.
 */
export function recoverySheet({ publicKey, encryptedPrivateKey, createdAt, passphraseHint }) {
  const ts = createdAt || new Date().toISOString()
  return `MailFlusher Ghost Inbox — Recovery Sheet
Generated: ${ts}

This sheet holds the OPENPGP KEY MATERIAL for your Ghost Inbox vault.
If you lose access to your account, someone with this sheet AND your vault
passphrase can still decrypt your stored emails. Store it somewhere safe.

${passphraseHint ? `\nPassphrase hint: ${passphraseHint}\n` : ''}
=====================================
PUBLIC KEY (safe to share)
=====================================

${publicKey}

=====================================
ENCRYPTED PRIVATE KEY (requires passphrase)
=====================================

${encryptedPrivateKey}

=====================================
How to recover manually
=====================================
If MailFlusher is ever unavailable, you can decrypt your stored emails using
any OpenPGP tool (Thunderbird, GnuPG CLI, Proton's OpenPGP.js playground, etc.):

  1. Paste the ENCRYPTED PRIVATE KEY into your OpenPGP tool
  2. Unlock it with your passphrase
  3. Paste the ciphertext blob of a stored email into the tool as a message
  4. The tool will output the decrypted email (MIME format)
`
}

export function downloadRecoverySheet(contents, filename = 'mailflusher-recovery.txt') {
  const blob = new Blob([contents], { type: 'text/plain' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}
