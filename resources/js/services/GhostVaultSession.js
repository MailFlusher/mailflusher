/**
 * Session-scoped unlocked-vault state with auto-lock on inactivity.
 *
 * The unlocked PrivateKey object sits in module-scope memory (not in
 * localStorage / sessionStorage / cookies). An inactivity timer locks the
 * vault after `lockMinutes`, clearing the reference. `recordActivity()` resets
 * the timer — the Ghost Inbox page calls it on every user interaction.
 */

let privateKey = null
let lockTimer = null
let lockMinutes = 15
let onLockCallback = null

export function setOnLock(cb) {
  onLockCallback = cb
}

export function setLockMinutes(minutes) {
  lockMinutes = Math.max(1, Math.min(1440, minutes || 15))
  if (privateKey) recordActivity()
}

export function unlock(key, minutes) {
  privateKey = key
  if (minutes) setLockMinutes(minutes)
  recordActivity()
}

export function getKey() {
  return privateKey
}

export function isUnlocked() {
  return !!privateKey
}

export function lock() {
  privateKey = null
  if (lockTimer) {
    clearTimeout(lockTimer)
    lockTimer = null
  }
  if (onLockCallback) onLockCallback()
}

export function recordActivity() {
  if (lockTimer) clearTimeout(lockTimer)
  lockTimer = setTimeout(lock, lockMinutes * 60 * 1000)
}
