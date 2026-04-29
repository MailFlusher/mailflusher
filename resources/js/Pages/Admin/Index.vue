<template>
  <div>
    <Head title="Control Panel" />

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-grey-900 dark:text-white">Control Panel</h1>
      <p class="mt-1 text-sm text-grey-500 dark:text-grey-400">
        Manage users and system settings. Admin access only.
      </p>
    </div>

    <div v-if="$page.props.flash" class="mb-6 rounded-lg border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 p-4">
      <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ $page.props.flash }}</p>
    </div>

    <!-- User Management -->
    <div class="bg-white dark:bg-grey-900 rounded-xl border border-grey-200 dark:border-grey-700 overflow-hidden">
      <div class="px-6 py-4 border-b border-grey-200 dark:border-grey-700">
        <h2 class="text-lg font-semibold text-grey-900 dark:text-white">Users</h2>
        <p class="text-sm text-grey-500 dark:text-grey-400 mt-1">
          {{ users.length }} registered {{ users.length === 1 ? 'user' : 'users' }}
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-grey-50 dark:bg-grey-800 border-b border-grey-200 dark:border-grey-700">
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Username</th>
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Email</th>
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Plan</th>
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Aliases</th>
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Bandwidth</th>
              <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Created</th>
              <th class="text-right px-6 py-3 text-xs font-semibold uppercase tracking-wider text-grey-500 dark:text-grey-400">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-grey-100 dark:divide-grey-800">
            <tr v-for="u in users" :key="u.id" class="hover:bg-grey-50 dark:hover:bg-grey-800/50">
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span class="font-medium text-grey-900 dark:text-white">{{ u.username }}</span>
                  <span v-if="u.is_admin" class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">admin</span>
                </div>
              </td>
              <td class="px-6 py-4 text-grey-600 dark:text-grey-300">{{ u.email }}</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                  :class="{
                    'bg-grey-100 text-grey-600 dark:bg-grey-800 dark:text-grey-400': u.plan === 'free',
                    'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300': u.plan === 'standard',
                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': u.plan === 'pro',
                  }"
                >{{ u.plan }}</span>
              </td>
              <td class="px-6 py-4 text-grey-600 dark:text-grey-300">{{ u.aliases_count }}</td>
              <td class="px-6 py-4 text-grey-600 dark:text-grey-300">{{ u.bandwidth }} MB</td>
              <td class="px-6 py-4 text-grey-500 dark:text-grey-400 text-xs">{{ $filters.timeAgo(u.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <button
                  v-if="!u.is_admin"
                  @click="confirmDelete(u)"
                  class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
                >
                  Delete
                </button>
                <span v-else class="text-grey-400 dark:text-grey-600 text-sm">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :open="deleteModalOpen" @close="deleteModalOpen = false">
      <template v-slot:title>Delete User Account</template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to permanently delete the account
          <strong>{{ userToDelete?.username }}</strong> ({{ userToDelete?.email }})?
        </p>
        <p class="mt-3 text-sm text-red-600 dark:text-red-400 font-medium">
          This action cannot be undone. All aliases, recipients, domains, rules, and account data will be permanently deleted.
        </p>

        <div class="mt-6">
          <label for="confirm-username" class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">
            Type the username <strong>{{ userToDelete?.username }}</strong> to confirm
          </label>
          <input
            id="confirm-username"
            v-model="confirmUsername"
            type="text"
            class="w-full rounded-lg border border-grey-300 dark:border-grey-600 bg-grey-50 dark:bg-grey-800 p-2.5 text-sm text-grey-900 dark:text-white focus:border-red-400 focus:ring-1 focus:ring-red-400"
            :placeholder="userToDelete?.username"
          />
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="deleteModalOpen = false"
            class="px-4 py-2 text-sm font-medium text-grey-700 dark:text-grey-200 bg-white dark:bg-grey-800 border border-grey-300 dark:border-grey-600 rounded-lg hover:bg-grey-50 dark:hover:bg-grey-700"
          >
            Cancel
          </button>
          <button
            @click="deleteUser"
            :disabled="confirmUsername !== userToDelete?.username || deleteLoading"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="deleteLoading">Deleting...</span>
            <span v-else>Delete Account</span>
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Modal from '../../Components/Modal.vue'

defineProps({
  users: {
    type: Array,
    required: true,
  },
})

const deleteModalOpen = ref(false)
const userToDelete = ref(null)
const confirmUsername = ref('')
const deleteLoading = ref(false)

const confirmDelete = user => {
  userToDelete.value = user
  confirmUsername.value = ''
  deleteModalOpen.value = true
}

const deleteUser = () => {
  if (confirmUsername.value !== userToDelete.value?.username) return

  deleteLoading.value = true

  axios
    .delete(`/admin/users/${userToDelete.value.id}`)
    .then(() => {
      deleteModalOpen.value = false
      deleteLoading.value = false
      router.reload()
    })
    .catch(error => {
      deleteLoading.value = false
      deleteModalOpen.value = false
      alert(error.response?.data?.message || error.response?.data || 'An error occurred')
    })
}
</script>
