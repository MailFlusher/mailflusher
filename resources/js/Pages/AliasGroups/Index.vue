<template>
  <div>
    <Head title="Groups" />

    <div class="sm:flex sm:items-center mb-6">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-grey-900 dark:text-white">Groups</h1>
        <p class="mt-2 text-sm text-grey-700 dark:text-grey-200">
          Organize your aliases into colored groups. Aliases can be filtered by group on the Aliases
          page. Groups can only be deleted when empty — move or delete aliases first.
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          @click="openCreate"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 font-bold shadow-sm"
        >
          New group
        </button>
      </div>
    </div>

    <div
      v-if="groups.length === 0"
      class="bg-white dark:bg-grey-900 border border-grey-200 dark:border-grey-700 rounded-lg p-10 text-center"
    >
      <p class="text-grey-500 dark:text-grey-400">
        You haven't created any groups yet. Create your first one to start organizing.
      </p>
    </div>

    <div v-else class="bg-white dark:bg-grey-900 border border-grey-200 dark:border-grey-700 rounded-lg overflow-hidden">
      <ul class="divide-y divide-grey-100 dark:divide-grey-800">
        <li
          v-for="group in groups"
          :key="group.id"
          class="flex items-center gap-4 p-4"
        >
          <GroupColorChip :name="group.name" :color="group.color" />
          <div class="flex-1 min-w-0">
            <p v-if="group.description" class="text-sm text-grey-600 dark:text-grey-300 truncate">
              {{ group.description }}
            </p>
            <p class="text-xs text-grey-400 dark:text-grey-500 mt-1">
              {{ group.aliases_count }} {{ group.aliases_count === 1 ? 'alias' : 'aliases' }}
            </p>
          </div>
          <div class="shrink-0 flex items-center gap-2">
            <Link
              :href="route('aliases.index', { group: group.id })"
              class="text-xs font-medium px-3 py-1.5 rounded border border-grey-200 dark:border-grey-600 text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-800"
            >View aliases</Link>
            <button
              @click="openEdit(group)"
              class="text-xs font-medium px-3 py-1.5 rounded border border-grey-200 dark:border-grey-600 text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-800"
            >Edit</button>
            <button
              @click="confirmDelete(group)"
              :disabled="group.aliases_count > 0"
              :title="group.aliases_count > 0 ? 'Move or remove the aliases in this group before deleting it' : ''"
              :class="[
                'text-xs font-medium px-3 py-1.5 rounded border',
                group.aliases_count > 0
                  ? 'border-grey-100 dark:border-grey-800 text-grey-300 dark:text-grey-600 cursor-not-allowed'
                  : 'border-red-200 text-red-600 hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-900/20',
              ]"
            >Delete</button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Create / edit modal -->
    <Modal :open="modalOpen" @close="modalOpen = false">
      <template v-slot:title>{{ editing ? 'Edit group' : 'Create group' }}</template>
      <template v-slot:content>
        <div class="mt-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">Name</label>
            <input
              v-model="form.name"
              type="text"
              maxlength="80"
              placeholder="e.g. Shopping"
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-1">Description (optional)</label>
            <input
              v-model="form.description"
              type="text"
              maxlength="200"
              placeholder="Anything to jog your memory later..."
              class="w-full rounded-md border-0 py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:ring-2 focus:ring-indigo-600 sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-grey-700 dark:text-grey-200 mb-2">Color</label>
            <div class="flex flex-wrap gap-2">
              <button
                type="button"
                v-for="c in palette"
                :key="c"
                @click="form.color = c"
                :class="[
                  'rounded-full h-8 w-8 border-2 transition-transform',
                  swatchClass(c),
                  form.color === c ? 'ring-2 ring-offset-2 ring-indigo-500 scale-110' : 'border-transparent hover:scale-110',
                ]"
                :title="c"
              ></button>
              <button
                type="button"
                @click="form.color = null"
                :class="[
                  'rounded-full h-8 w-8 border-2 bg-white dark:bg-grey-800 flex items-center justify-center text-grey-400',
                  form.color === null ? 'ring-2 ring-offset-2 ring-indigo-500 border-grey-300' : 'border-grey-200 hover:border-grey-300',
                ]"
                title="no color"
              >
                <span class="text-xs">—</span>
              </button>
            </div>
          </div>
          <p v-if="formError" class="text-sm text-red-600">{{ formError }}</p>
        </div>
        <div class="mt-6 flex gap-3">
          <button
            @click="submit"
            :disabled="loading"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:opacity-50"
          >
            {{ loading ? 'Saving…' : editing ? 'Save' : 'Create group' }}
          </button>
          <button
            @click="modalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <!-- Delete confirm -->
    <Modal :open="!!groupToDelete" @close="groupToDelete = null">
      <template v-slot:title>Delete group?</template>
      <template v-slot:content>
        <p class="text-sm text-grey-700 dark:text-grey-200 mt-4">
          Delete <strong>{{ groupToDelete?.name }}</strong>? This cannot be undone.
        </p>
        <div class="mt-6 flex gap-3">
          <button
            @click="doDelete"
            class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-4 rounded"
          >
            Delete
          </button>
          <button
            @click="groupToDelete = null"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:bg-grey-600 dark:hover:bg-grey-700 border border-grey-200 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import GroupColorChip from '@/Components/GroupColorChip.vue'

const props = defineProps({
  initialGroups: { type: Array, default: () => [] },
  palette: { type: Array, default: () => [] },
})

const groups = ref([...props.initialGroups])
const modalOpen = ref(false)
const editing = ref(null) // null or group object
const loading = ref(false)
const formError = ref('')
const form = reactive({ name: '', description: '', color: null })
const groupToDelete = ref(null)

const SWATCH = {
  indigo: 'bg-indigo-500',
  cyan: 'bg-cyan-500',
  green: 'bg-green-500',
  amber: 'bg-amber-500',
  red: 'bg-red-500',
  purple: 'bg-purple-500',
  pink: 'bg-pink-500',
  grey: 'bg-grey-500',
}
const swatchClass = c => SWATCH[c] || 'bg-grey-300'

const openCreate = () => {
  editing.value = null
  form.name = ''
  form.description = ''
  form.color = null
  formError.value = ''
  modalOpen.value = true
}

const openEdit = group => {
  editing.value = group
  form.name = group.name
  form.description = group.description || ''
  form.color = group.color
  formError.value = ''
  modalOpen.value = true
}

const submit = () => {
  if (!form.name.trim()) {
    formError.value = 'Name is required.'
    return
  }
  loading.value = true
  formError.value = ''
  const payload = {
    name: form.name.trim(),
    description: form.description || null,
    color: form.color,
  }
  const request = editing.value
    ? axios.patch(`/api/v1/alias-groups/${editing.value.id}`, payload)
    : axios.post('/api/v1/alias-groups', payload)

  request
    .then(({ data }) => {
      const incoming = data.data
      if (editing.value) {
        const idx = groups.value.findIndex(g => g.id === incoming.id)
        if (idx !== -1) groups.value[idx] = incoming
      } else {
        groups.value.push(incoming)
        groups.value.sort((a, b) => (a.sort_order - b.sort_order) || a.name.localeCompare(b.name))
      }
      modalOpen.value = false
      loading.value = false
    })
    .catch(error => {
      loading.value = false
      formError.value = error.response?.data?.errors?.name?.[0] || error.response?.data?.message || 'Could not save.'
    })
}

const confirmDelete = group => {
  if (group.aliases_count > 0) return
  groupToDelete.value = group
}

const doDelete = () => {
  if (!groupToDelete.value) return
  const id = groupToDelete.value.id
  axios.delete(`/api/v1/alias-groups/${id}`).then(() => {
    groups.value = groups.value.filter(g => g.id !== id)
    groupToDelete.value = null
  }).catch(() => {
    groupToDelete.value = null
  })
}
</script>
