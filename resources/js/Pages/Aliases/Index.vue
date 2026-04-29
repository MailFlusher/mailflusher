<template>
  <div>
    <Head title="Aliases" />
    <h1 id="primary-heading" class="sr-only">Aliases</h1>

    <div class="sm:flex sm:items-center mb-6">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-grey-900 dark:text-white">Aliases</h1>
        <p class="mt-2 text-sm text-grey-700 dark:text-grey-200">
          A list of all the aliases
          {{
            Object.keys(route().params).length || currentAliasStatus !== 'active_inactive'
              ? 'found for your search or filters'
              : 'in your account'
          }}
          <button @click="moreInfoOpen = !moreInfoOpen">
            <InformationCircleIcon
              class="h-6 w-6 inline-block cursor-pointer text-grey-500 dark:text-grey-200"
              title="Click for more information"
            />
          </button>
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex items-center">
        <button
          type="button"
          @click="createAliasModalOpen = true"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 font-bold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:w-auto"
        >
          Create Alias
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div
      v-if="
        rows.length ||
        Object.keys(route().params).length ||
        currentAliasStatus !== 'active_inactive'
      "
      class="flex flex-col sm:flex-row justify-between items-center mb-4 bg-white rounded-lg shadow dark:bg-grey-900"
    >
      <div class="relative py-4 flex items-center space-x-1.5 px-4 text-sm sm:px-6">
        <Listbox as="div" v-model="showAliasStatus">
          <div class="relative">
            <div>
              <ListboxButton
                class="inline-flex items-center text-sm text-grey-700 hover:text-grey-900 rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-grey-200 dark:hover:text-grey-300"
              >
                <span class="sr-only">Change display</span>
                <ListboxLabel class="cursor-pointer">Display</ListboxLabel>
                <p class="ml-1 font-medium">{{ showAliasStatus.label }}</p>
                <ChevronDownIcon
                  class="h-5 w-5 text-grey-700 dark:text-grey-200"
                  aria-hidden="true"
                />
              </ListboxButton>
            </div>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="absolute z-20 mt-2 w-48 origin-top-left overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-grey-900"
              >
                <ListboxOption
                  as="template"
                  v-for="option in displayOptions"
                  :key="option.value"
                  :value="option"
                  v-slot="{ active, selected }"
                >
                  <li
                    :class="[
                      active ? 'text-white bg-indigo-500' : 'text-grey-900 dark:text-grey-100',
                      'cursor-pointer select-none p-2 text-sm',
                    ]"
                  >
                    <div class="flex flex-col">
                      <div class="flex justify-between">
                        <p :class="selected ? 'font-semibold' : 'font-normal'">
                          {{ option.label }}
                        </p>
                        <span
                          v-if="selected"
                          :class="active ? 'text-white' : 'text-indigo-500 dark:text-grey-100'"
                        >
                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
                        </span>
                      </div>
                    </div>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
        <span
          v-if="['all', 'active_inactive', 'active'].includes(showAliasStatus.value)"
          class="bg-green-100 tooltip outline-none h-4 w-4 rounded-full flex items-center justify-center"
          data-tippy-content="Active"
          tabindex="-1"
          ><span class="bg-green-400 h-2 w-2 rounded-full"></span
        ></span>
        <span
          v-if="['all', 'active_inactive', 'inactive'].includes(showAliasStatus.value)"
          class="bg-grey-100 tooltip outline-none h-4 w-4 rounded-full flex items-center justify-center"
          data-tippy-content="Inactive"
          tabindex="-1"
          ><span class="bg-grey-400 h-2 w-2 rounded-full"></span
        ></span>
        <span
          v-if="['all', 'deleted'].includes(showAliasStatus.value)"
          class="bg-red-100 tooltip outline-none h-4 w-4 rounded-full flex items-center justify-center"
          data-tippy-content="Deleted"
          tabindex="-1"
          ><span class="bg-red-400 h-2 w-2 rounded-full"></span
        ></span>
        <Listbox as="div" v-model="selectedPinnedFilter" class="ml-1">
          <div class="relative">
            <ListboxButton
              class="inline-flex items-center text-sm rounded px-2 py-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 text-grey-700 hover:text-grey-900 dark:text-grey-200 dark:hover:text-grey-300"
            >
              <span class="sr-only">Pin filter</span>
              <span>{{ selectedPinnedFilter.label }} </span>
              <ChevronDownIcon
                class="ml-1 h-4 w-4 text-grey-500 dark:text-grey-400"
                aria-hidden="true"
              />
              <span
                class="ml-1 outline-none inline-flex items-center"
                :class="
                  selectedPinnedFilter.value === 'unpinned'
                    ? 'text-grey-500 dark:text-grey-400'
                    : 'text-yellow-500 dark:text-yellow-400'
                "
                aria-hidden="true"
              >
                <icon
                  name="pin"
                  class="inline-block w-4 h-4"
                  :class="
                    selectedPinnedFilter.value === 'unpinned' ? 'stroke-current' : 'fill-current'
                  "
                />
              </span>
            </ListboxButton>
            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="absolute right-0 left-auto z-20 mt-1 w-40 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-grey-900"
              >
                <ListboxOption
                  as="template"
                  v-for="option in pinnedFilterOptions"
                  :key="option.value"
                  :value="option"
                  v-slot="{ active, selected }"
                >
                  <li
                    :class="[
                      active ? 'text-white bg-indigo-500' : 'text-grey-900 dark:text-grey-100',
                      'cursor-pointer select-none p-2 text-sm',
                    ]"
                  >
                    <div class="flex justify-between items-center">
                      <span :class="selected ? 'font-semibold' : 'font-normal'">
                        {{ option.label }}
                      </span>
                      <CheckIcon
                        v-if="selected"
                        :class="active ? 'text-white' : 'text-indigo-500 dark:text-grey-100'"
                        class="h-5 w-5"
                        aria-hidden="true"
                      />
                    </div>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
      </div>
      <div class="flex py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
          <Listbox as="div" v-model="currentSort">
            <div class="relative">
              <div>
                <ListboxButton
                  class="inline-flex items-center text-sm text-grey-700 hover:text-grey-900 rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-grey-200 dark:hover:text-grey-300"
                >
                  <span class="sr-only">Change sort by</span>
                  <ListboxLabel class="cursor-pointer">Sort By</ListboxLabel>
                  <p class="ml-1 font-medium">{{ currentSort.label }}</p>
                  <ChevronDownIcon
                    class="h-5 w-5 text-grey-700 dark:text-grey-200"
                    aria-hidden="true"
                  />
                </ListboxButton>
              </div>

              <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
              >
                <ListboxOptions
                  class="absolute right-0 z-20 mt-2 w-48 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-grey-900"
                >
                  <ListboxOption
                    as="template"
                    v-for="option in sortOptions"
                    :key="option.value"
                    :value="option"
                    v-slot="{ active, selected }"
                  >
                    <li
                      :class="[
                        active ? 'text-white bg-indigo-500' : 'text-grey-900 dark:text-grey-100',
                        'cursor-pointer select-none p-2 text-sm',
                      ]"
                    >
                      <div class="flex flex-col">
                        <div class="flex justify-between">
                          <p :class="selected ? 'font-semibold' : 'font-normal'">
                            {{ option.label }}
                          </p>
                          <span
                            v-if="selected"
                            :class="active ? 'text-white' : 'text-indigo-500 dark:text-grey-100'"
                          >
                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                          </span>
                        </div>
                      </div>
                    </li>
                  </ListboxOption>
                </ListboxOptions>
              </transition>
            </div>
          </Listbox>

          <button
            class="ml-3 disabled:cursor-not-allowed tooltip"
            :disabled="changeSortDirLoading"
            @click="changeSortDir()"
            :data-tippy-content="
              $page.props.sortDirection === 'desc' ? 'Change to ascending' : 'Change to descending'
            "
          >
            <BarsArrowDownIcon v-if="$page.props.sortDirection === 'desc'" class="h-5 w-5" />
            <BarsArrowUpIcon type="button" v-else class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Active group filter banner -->
    <div
      v-if="activeGroup"
      class="mb-3 flex items-center gap-3 rounded-md bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 px-4 py-2"
    >
      <span class="text-sm text-indigo-700 dark:text-indigo-300">Showing aliases in group:</span>
      <GroupColorChip :name="activeGroup.name" :color="activeGroup.color" />
      <Link
        :href="clearGroupFilterHref"
        class="ml-auto text-xs font-medium text-indigo-700 dark:text-indigo-300 hover:underline"
      >Clear filter</Link>
    </div>

    <div v-if="rows.length">
      <div class="relative aliases-table">
        <div
          v-if="selectedRows.length > 0"
          id="bulk-actions"
          class="horizontal-scroll sticky top-16 left-0 px-0.5 pl-12 flex flex-nowrap h-12 items-center space-x-3 bg-white dark:bg-grey-900 z-30 overflow-x-auto border-b border-grey-200 dark:border-grey-700"
        >
          <button
            type="button"
            class="ml-1 inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkActivate() || bulkActivateAliasLoading"
            @click="bulkActivateAlias()"
          >
            Activate <loader v-if="bulkActivateAliasLoading" />
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkDeactivate() || bulkDeactivateAliasLoading"
            @click="bulkDeactivateAlias()"
          >
            Deactivate <loader v-if="bulkDeactivateAliasLoading" />
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkPin() || bulkPinAliasLoading"
            @click="selectedRows.length === 1 ? pinAlias(selectedRows[0]) : bulkPinAlias()"
          >
            Pin <loader v-if="bulkPinAliasLoading" />
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkUnpin() || bulkUnpinAliasLoading"
            @click="selectedRows.length === 1 ? unpinAlias(selectedRows[0]) : bulkUnpinAlias()"
          >
            Unpin <loader v-if="bulkUnpinAliasLoading" />
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700 whitespace-nowrap"
            :disabled="bulkEditAliasRecipientsLoading"
            @click="
              selectedRows.length === 1
                ? openAliasRecipientsModal(selectedRows[0])
                : openBulkAliasRecipientsModal()
            "
          >
            Edit Recipients <loader v-if="bulkEditAliasRecipientsLoading" />
          </button>
          <button
            type="button"
            v-if="aliasGroups.length > 0"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            @click="bulkGroupModalOpen = true"
          >
            Move to group
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkDelete()"
            @click="
              selectedAliasesToDelete.length === 1
                ? openDeleteModal(selectedAliasesToDelete[0])
                : (bulkDeleteAliasModalOpen = true)
            "
          >
            Delete
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            @click="
              selectedRowIds.length === 1
                ? openForgetModal(selectedRows[0])
                : (bulkForgetAliasModalOpen = true)
            "
          >
            Forget
          </button>
          <button
            type="button"
            class="inline-flex items-center rounded border border-grey-300 bg-white px-2.5 py-1.5 text-xs font-medium text-grey-700 shadow-sm hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30 dark:border-grey-600 dark:bg-grey-800 dark:text-grey-200 dark:hover:bg-grey-700"
            :disabled="disabledBulkRestore()"
            @click="
              selectedAliasesToRestore.length === 1
                ? openRestoreModal(selectedAliasesToRestore[0])
                : (bulkRestoreAliasModalOpen = true)
            "
          >
            Restore
          </button>
          <span class="font-semibold text-indigo-800 hidden md:inline-block dark:text-indigo-400">{{
            selectedRows.length === 1
              ? `${selectedRows.length} alias`
              : `${selectedRows.length} aliases`
          }}</span>
        </div>
        <vue-good-table
          v-on:sort-change="debounceToolips"
          v-on:page-change="debounceToolips"
          v-on:per-page-change="debounceToolips"
          :columns="columns"
          :rows="rows"
          :sort-options="{
            enabled: false,
          }"
          styleClass="vgt-table"
          :row-style-class="rowStyleClassFn"
        >
          <template #table-column="props">
            <span v-if="props.column.field == 'select'">
              <input
                v-if="rows.length <= 25"
                type="checkbox"
                class="h-4 w-4 rounded border-grey-300 text-indigo-600 focus:ring-indigo-500 sm:left-6 dark:text-indigo-400 dark:bg-grey-950"
                :checked="indeterminate || selectedRowIds.length === rows.length"
                :indeterminate="indeterminate"
                @change="selectedRowIds = $event.target.checked ? rows.map(r => r.id) : []"
              />
              <div
                v-else
                type="checkbox"
                class="h-4 w-4 rounded border-grey-300 bg-grey-100 border text-indigo-600 focus:ring-indigo-500 sm:left-6 tooltip cursor-not-allowed dark:bg-grey-800"
                data-tippy-content="'Select All' is only available when the page size is 25"
              ></div>
            </span>
            <span v-else-if="props.column.label == 'Active'">
              {{ props.column.label }}
              <span
                class="tooltip outline-none"
                data-tippy-content="When an alias is deactivated, any messages sent to it will be silently discarded. The sender will not be notified of the unsuccessful delivery."
              >
                <icon name="info" class="inline-block w-4 h-4 text-grey-300 fill-current" />
              </span>
            </span>
            <span v-else>
              {{ props.column.label }}
            </span>
          </template>
          <template #table-row="props">
            <span v-if="props.column.field === 'select'" class="flex items-center">
              <div
                v-if="selectedRowIds.includes(props.row.id)"
                class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"
              ></div>
              <div
                v-if="selectedRowIds.length >= 25 && !selectedRowIds.includes(props.row.id)"
                type="checkbox"
                class="h-4 w-4 rounded border-grey-300 bg-grey-100 text-indigo-600 focus:ring-indigo-500 sm:left-6 cursor-not-allowed dark:bg-grey-900"
                title="You cannot select more than 25 aliases"
              ></div>
              <input
                v-else
                type="checkbox"
                class="h-4 w-4 rounded border-grey-300 text-indigo-600 focus:ring-indigo-500 sm:left-6 dark:bg-grey-900"
                title="Click to select'"
                :value="props.row.id"
                v-model="selectedRowIds"
              />
            </span>
            <span v-else-if="props.column.field == 'created_at'" class="flex items-center">
              <span
                :class="`bg-${getAliasStatus(props.row).colour}-100`"
                class="tooltip outline-none h-4 w-4 rounded-full flex items-center justify-center mr-2"
                :data-tippy-content="getAliasStatus(props.row).status"
                tabindex="-1"
              >
                <span
                  :class="`bg-${getAliasStatus(props.row).colour}-400`"
                  class="h-2 w-2 rounded-full"
                ></span>
              </span>
              <span
                class="tooltip outline-none cursor-default text-sm whitespace-nowrap text-grey-500 dark:text-grey-300"
                :data-tippy-content="$filters.formatDate(rows[props.row.originalIndex].created_at)"
                >{{ $filters.timeAgo(props.row.created_at) }}
              </span>
            </span>
            <span v-else-if="props.column.field == 'email'" class="block">
              <div class="flex items-center">
                <span
                  v-if="props.row.pinned"
                  class="mr-1 tooltip outline-none inline-flex items-center text-yellow-500 dark:text-yellow-400 cursor-default"
                  data-tippy-content="Pinned"
                  aria-hidden="true"
                >
                  <icon name="pin" class="inline-block w-4 h-4 fill-current" />
                </span>
                <span
                  v-if="isBurnerAlias(props.row)"
                  class="tooltip outline-none mr-2 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide cursor-default"
                  :class="burnerBadgeClasses(props.row)"
                  :data-tippy-content="burnerTooltip(props.row)"
                  >{{ burnerBadgeLabel(props.row) }}</span
                >
                <Link
                  v-if="props.row.group"
                  :href="route('aliases.index', { group: props.row.group.id })"
                  class="mr-2"
                >
                  <GroupColorChip :name="props.row.group.name" :color="props.row.group.color" />
                </Link>
                <button
                  class="text-grey-400 tooltip outline-none text-left"
                  data-tippy-content="Click to copy"
                  @click="clipboard(getAliasEmail(rows[props.row.originalIndex]))"
                >
                  <span class="font-semibold text-indigo-800 dark:text-indigo-400">{{
                    $filters.truncate(getAliasLocalPart(props.row), 60)
                  }}</span
                  ><span
                    v-if="getAliasLocalPart(props.row).length <= 60"
                    class="font-semibold text-grey-500 dark:text-grey-200"
                    >{{
                      $filters.truncate(
                        '@' + props.row.domain,
                        60 - getAliasLocalPart(props.row).length,
                      )
                    }}</span
                  >
                </button>
              </div>
              <div v-if="aliasIdToEdit === props.row.id" class="flex items-center">
                <input
                  @keyup.enter="editAliasDescription(rows[props.row.originalIndex])"
                  @keyup.esc="aliasIdToEdit = aliasDescriptionToEdit = ''"
                  v-model="aliasDescriptionToEdit"
                  type="text"
                  class="grow text-sm appearance-none bg-grey-50 border text-grey-700 dark:text-grey-300 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 px-2 py-1 dark:bg-white/5"
                  :class="
                    aliasDescriptionToEdit.length > 200 ? 'border-red-500' : 'border-transparent'
                  "
                  placeholder="Add description"
                  tabindex="0"
                  autofocus
                />
                <button @click="aliasIdToEdit = aliasDescriptionToEdit = ''" aria-label="Cancel">
                  <icon name="close" class="inline-block w-6 h-6 text-red-300 fill-current" />
                </button>
                <button
                  @click="editAliasDescription(rows[props.row.originalIndex])"
                  aria-label="Save"
                >
                  <icon name="save" class="inline-block w-6 h-6 text-indigo-500 fill-current" />
                </button>
              </div>
              <div v-else-if="props.row.description" class="flex items-center">
                <span
                  class="inline-block text-grey-400 text-sm py-1 border border-transparent mr-2 dark:text-grey-300"
                >
                  {{ $filters.truncate(props.row.description, 60) }}
                </span>
                <button
                  @click="
                    ;((aliasIdToEdit = props.row.id),
                      (aliasDescriptionToEdit = props.row.description))
                  "
                  aria-label="Edit"
                >
                  <icon name="edit" class="inline-block w-6 h-6 text-grey-300 fill-current" />
                </button>
              </div>
              <div v-else>
                <button
                  class="inline-block text-grey-300 text-sm py-1 border border-transparent"
                  @click=";((aliasIdToEdit = props.row.id), (aliasDescriptionToEdit = ''))"
                >
                  Add description
                </button>
              </div>
              <div class="md:hidden mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-grey-500 dark:text-grey-400">
                <span :title="$filters.formatDate(rows[props.row.originalIndex].created_at)">
                  📅 {{ $filters.timeAgo(props.row.created_at) }}
                </span>
                <span>📨 {{ props.row.emails_forwarded.toLocaleString() }}</span>
                <span v-if="props.row.emails_blocked > 0">⛔ {{ props.row.emails_blocked.toLocaleString() }}</span>
                <span v-if="props.row.emails_replied > 0">↩︎ {{ props.row.emails_replied.toLocaleString() }}</span>
                <span v-if="props.row.emails_sent > 0">✉ {{ props.row.emails_sent.toLocaleString() }}</span>
              </div>
            </span>
            <span
              v-else-if="props.column.field == 'recipients'"
              class="flex items-center justify-center"
            >
              <span
                v-if="props.row.recipients.length && props.row.id !== recipientsAliasToEdit.id"
                class="inline-block tooltip outline-none font-semibold text-indigo-800 dark:text-indigo-400"
                :data-tippy-content="recipientsTooltip(props.row.recipients)"
              >
                {{ props.row.recipients.length }}
              </span>
              <span
                v-else-if="props.row.id === recipientsAliasToEdit.id"
                class="inline-block outline-none font-semibold text-indigo-800 dark:text-indigo-400"
                >{{ aliasRecipientsToEdit.length ? aliasRecipientsToEdit.length : '1' }}</span
              >
              <span
                v-else-if="has(props.row.aliasable, 'default_recipient.email')"
                class="py-1 px-2 text-xs bg-yellow-200 text-yellow-900 rounded-full tooltip outline-none cursor-default"
                :data-tippy-content="props.row.aliasable.default_recipient.email"
                >{{
                  props.row.aliasable_type === 'App\\Models\\Domain' ? 'domain' : 'username'
                }}'s</span
              >
              <span
                v-else
                class="py-1 px-2 text-xs bg-yellow-200 text-yellow-900 rounded-full tooltip outline-none cursor-default"
                :data-tippy-content="$page.props.user.email"
                >default</span
              >
              <button @click="openAliasRecipientsModal(props.row)" class="ml-2">
                <icon name="edit" class="inline-block w-6 h-6 text-grey-300 fill-current" />
              </button>
            </span>
            <span
              v-else-if="props.column.field == 'emails_forwarded'"
              class="font-semibold text-indigo-800 dark:text-indigo-400"
            >
              <span
                v-if="props.row.last_forwarded"
                class="tooltip outline-none cursor-default"
                :data-tippy-content="
                  $filters.timeAgo(props.row.last_forwarded) +
                  ' (' +
                  $filters.formatDate(rows[props.row.originalIndex].last_forwarded) +
                  ')'
                "
                >{{ props.row.emails_forwarded.toLocaleString() }}
              </span>
              <span v-else>{{ props.row.emails_forwarded.toLocaleString() }} </span>
              <span class="text-grey-300 mx-1.5">/</span>
              <span
                v-if="props.row.last_blocked"
                class="tooltip outline-none cursor-default"
                :data-tippy-content="
                  $filters.timeAgo(props.row.last_blocked) +
                  ' (' +
                  $filters.formatDate(rows[props.row.originalIndex].last_blocked) +
                  ')'
                "
                >{{ props.row.emails_blocked.toLocaleString() }}
              </span>
              <span v-else>{{ props.row.emails_blocked.toLocaleString() }} </span>
            </span>
            <span
              v-else-if="props.column.field == 'emails_replied'"
              class="font-semibold text-indigo-800 dark:text-indigo-400"
            >
              <span
                v-if="props.row.last_replied"
                class="tooltip outline-none cursor-default"
                :data-tippy-content="
                  $filters.timeAgo(props.row.last_replied) +
                  ' (' +
                  $filters.formatDate(rows[props.row.originalIndex].last_replied) +
                  ')'
                "
                >{{ props.row.emails_replied.toLocaleString() }}
              </span>
              <span v-else>{{ props.row.emails_replied.toLocaleString() }} </span>
              <span class="text-grey-300 mx-1.5">/</span>
              <span
                v-if="props.row.last_sent"
                class="tooltip outline-none cursor-default"
                :data-tippy-content="
                  $filters.timeAgo(props.row.last_sent) +
                  ' (' +
                  $filters.formatDate(rows[props.row.originalIndex].last_sent) +
                  ')'
                "
                >{{ props.row.emails_sent.toLocaleString() }}
              </span>
              <span v-else>{{ props.row.emails_sent.toLocaleString() }} </span>
            </span>
            <span v-else-if="props.column.field === 'active'" class="flex items-center">
              <Toggle
                v-model="rows[props.row.originalIndex].active"
                @on="activateAlias(rows[props.row.originalIndex])"
                @off="deactivateAlias(rows[props.row.originalIndex])"
              />
            </span>
            <span v-else class="flex items-center justify-center outline-none" tabindex="-1">
              <Link
                :href="route('aliases.edit', props.row.id)"
                as="button"
                type="button"
                class="text-indigo-500 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-500 font-medium"
                >Edit<span class="sr-only">, {{ props.row.email }}</span></Link
              >
              <button
                @click="openSendFromModal(props.row)"
                class="group flex items-center text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-500 hover:text-indigo-800 font-medium ml-4 tooltip"
                data-tippy-content="Send an email from this alias"
              >
                Send
                <EnvelopeIcon class="ml-1 h-4 w-4" aria-hidden="true" />
              </button>
            </span>
          </template>
        </vue-good-table>

        <div
          class="mt-4 rounded-lg shadow flex items-center justify-between bg-white px-4 py-3 sm:px-6 overflow-x-auto horizontal-scroll dark:bg-grey-900"
        >
          <div class="flex flex-1 justify-between items-center md:hidden gap-x-3">
            <Link
              v-if="$page.props.initialRows.prev_page_url"
              :href="$page.props.initialRows.prev_page_url"
              as="button"
              class="relative inline-flex items-center rounded-md border border-grey-300 bg-white px-4 py-2 text-sm font-medium text-grey-700 hover:bg-grey-50 dark:bg-grey-950 dark:hover:bg-grey-900 dark:text-grey-200"
            >
              Previous
            </Link>
            <span
              v-else
              class="relative inline-flex h-min items-center rounded-md border border-grey-300 px-4 py-2 text-sm font-medium text-grey-700 bg-grey-100 dark:bg-grey-800 dark:text-grey-200"
              >Previous</span
            >
            <div class="flex flex-col items-center justify-center gap-y-2">
              <p class="text-sm text-grey-700 text-center dark:text-grey-200">
                Showing
                {{ ' ' }}
                <span class="font-medium">{{ $page.props.initialRows.from.toLocaleString() }}</span>
                {{ ' ' }}
                to
                {{ ' ' }}
                <span class="font-medium">{{ $page.props.initialRows.to.toLocaleString() }}</span>
                {{ ' ' }}
                of
                {{ ' ' }}
                <span class="font-medium">{{
                  $page.props.initialRows.total.toLocaleString()
                }}</span>
                {{ ' ' }}
                {{ $page.props.initialRows.total === 1 ? 'result' : 'results' }}
              </p>
              <select
                v-model.number="pageSize"
                @change="updatePageSize"
                :disabled="updatePageSizeLoading"
                class="relative rounded border-0 bg-transparent py-1 pr-8 text-grey-900 text-sm ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset ring-grey-300 focus:ring-indigo-600 disabled:cursor-not-allowed dark:text-grey-200"
              >
                <option v-for="size in pageSizeOptions" :value="size" class="dark:text-grey-200">
                  {{ size }}
                </option>
              </select>
            </div>
            <Link
              v-if="$page.props.initialRows.next_page_url"
              :href="$page.props.initialRows.next_page_url"
              as="button"
              class="relative inline-flex h-min items-center rounded-md border border-grey-300 bg-white px-4 py-2 text-sm font-medium text-grey-700 hover:bg-grey-50 dark:bg-grey-950 dark:hover:bg-grey-900 dark:text-grey-200"
            >
              Next
            </Link>
            <span
              v-else
              class="relative inline-flex items-center rounded-md border border-grey-300 px-4 py-2 text-sm font-medium text-grey-700 bg-grey-100 dark:bg-grey-800 dark:text-grey-200"
              >Next</span
            >
          </div>
          <div class="hidden md:flex md:flex-1 md:items-center md:justify-between md:gap-x-2">
            <div class="flex items-center gap-x-2">
              <p class="text-sm text-grey-700 dark:text-grey-200">
                Showing
                {{ ' ' }}
                <span class="font-medium">{{ $page.props.initialRows.from.toLocaleString() }}</span>
                {{ ' ' }}
                to
                {{ ' ' }}
                <span class="font-medium">{{ $page.props.initialRows.to.toLocaleString() }}</span>
                {{ ' ' }}
                of
                {{ ' ' }}
                <span class="font-medium">{{
                  $page.props.initialRows.total.toLocaleString()
                }}</span>
                {{ ' ' }}
                {{ $page.props.initialRows.total === 1 ? 'result' : 'results' }}
              </p>
              <select
                v-model.number="pageSize"
                @change="updatePageSize"
                :disabled="updatePageSizeLoading"
                class="relative rounded border-0 bg-transparent py-1 pr-8 text-grey-900 text-sm ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset ring-grey-300 focus:ring-indigo-600 disabled:cursor-not-allowed dark:text-grey-200"
              >
                <option v-for="size in pageSizeOptions" :value="size" class="dark:bg-grey-900">
                  {{ size }}
                </option>
              </select>
            </div>

            <nav
              class="isolate inline-flex -space-x-px rounded-md shadow-sm break-"
              aria-label="Pagination"
            >
              <Link
                v-if="$page.props.initialRows.prev_page_url"
                :href="$page.props.initialRows.prev_page_url"
                class="relative inline-flex items-center rounded-l-md border border-grey-300 bg-white px-2 py-2 text-sm font-medium text-grey-500 hover:bg-grey-50 focus:z-20 dark:bg-grey-900 dark:hover:bg-grey-950 dark:border-grey-500"
              >
                <span class="sr-only">Previous</span>
                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
              </Link>
              <span
                v-else
                class="disabled cursor-not-allowed relative inline-flex items-center rounded-l-md border border-grey-300 bg-white px-2 py-2 text-sm font-medium text-grey-500 hover:bg-grey-50 focus:z-20 dark:bg-grey-800 dark:border-grey-500"
              >
                <span class="sr-only">Previous</span>
                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
              </span>

              <div v-for="link in links" v-bind:key="link.label">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  aria-current="page"
                  class="relative inline-flex items-center border z-10 px-4 py-2 text-sm font-medium focus:z-20"
                  :class="
                    link.active
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:bg-grey-950 dark:text-grey-100 dark:border-grey-500'
                      : 'border-grey-300 bg-white text-grey-500 hover:bg-grey-50 dark:bg-grey-900 dark:hover:bg-grey-950 dark:text-grey-200 dark:border-grey-500'
                  "
                  >{{ link.label }}</Link
                >
                <span
                  v-else
                  class="relative inline-flex items-center border border-grey-300 bg-white px-4 py-2 text-sm font-medium text-grey-700 dark:bg-grey-900 dark:text-grey-200 dark:border-grey-500"
                  >...</span
                >
              </div>

              <Link
                v-if="$page.props.initialRows.next_page_url"
                :href="$page.props.initialRows.next_page_url"
                class="relative inline-flex items-center rounded-r-md border border-grey-300 bg-white px-2 py-2 text-sm font-medium text-grey-500 hover:bg-grey-50 focus:z-20 dark:bg-grey-900 dark:hover:bg-grey-950 dark:border-grey-500"
              >
                <span class="sr-only">Next</span>
                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
              </Link>
              <span
                v-else
                class="disabled cursor-not-allowed relative inline-flex items-center rounded-r-md border border-grey-300 bg-white px-2 py-2 text-sm font-medium text-grey-500 hover:bg-grey-50 focus:z-20 dark:bg-grey-800 dark:text-grey-200 dark:border-grey-500"
              >
                <span class="sr-only">Next</span>
                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
              </span>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div
      v-else-if="
        Object.keys(route().params).length ||
        currentAliasStatus !== 'active_inactive' ||
        activeGroupFilter
      "
      class="text-center"
    >
      <AtSymbolIcon class="mx-auto h-16 w-16 text-grey-400 dark:text-grey-200" />
      <h3 class="mt-2 text-lg font-medium text-grey-900 dark:text-white">
        <template v-if="activeGroup">No aliases in group "{{ activeGroup.name }}" yet</template>
        <template v-else>No Aliases found for that search or with those filters</template>
      </h3>
      <p class="mt-1 text-md text-grey-500 dark:text-grey-200">
        <template v-if="activeGroup">
          Create a new alias and assign it to this group, or move existing aliases in from
          <Link :href="route('aliases.index')" class="text-indigo-700 dark:text-indigo-400 underline">all aliases</Link>.
        </template>
        <template v-else>
          Try entering a different search term or changing the filters.
        </template>
      </p>
      <div class="mt-6">
        <Link
          :href="route('aliases.index')"
          type="button"
          class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 text-sm font-medium shadow-sm focus:outline-none"
        >
          View All Aliases
        </Link>
      </div>
    </div>

    <div v-else class="text-center text-grey-700 dark:text-grey-200">
      <AtSymbolIcon class="mx-auto h-16 w-16 text-grey-400 dark:text-grey-200" />
      <h3 class="mt-2 text-lg font-medium text-grey-900 dark:text-white">
        It doesn't look like you have any aliases yet!
      </h3>
      <div v-if="deletedAliasCount > 0" class="mt-4 mb-6">
        <p class="text-sm text-grey-500 dark:text-grey-400 mb-3">
          You have {{ deletedAliasCount }} deleted {{ deletedAliasCount === 1 ? 'alias' : 'aliases' }} that can be restored.
        </p>
        <Link
          :href="route('aliases.index', { deleted: 'only' })"
          class="inline-flex items-center rounded-md border border-grey-300 dark:border-grey-600 bg-white dark:bg-grey-800 px-4 py-2 text-sm font-medium text-grey-700 dark:text-grey-200 shadow-sm hover:bg-grey-50 dark:hover:bg-grey-700"
        >
          Show deleted aliases
        </Link>
      </div>
      <div v-if="subdomain">
        <p class="mb-4 text-md">
          There {{ domain ? 'are two ways' : 'is one way' }} to create new aliases.
        </p>
        <h3 class="mb-2 text-lg text-indigo-800 dark:text-indigo-400 font-semibold">
          Create aliases on the fly
        </h3>
        <p class="mb-2">
          To create aliases on the fly all you have to do is make up any new alias and give that out
          instead of your real email address.
        </p>
        <p class="mb-2">
          Let's say you're signing up to <b>example.com</b> you could enter
          <b>example@{{ subdomain }}</b> as your email address.
        </p>
        <p class="mb-2">
          The alias will show up here automatically as soon as it has forwarded its first email.
        </p>
        <p class="mb-2">
          If you start receiving spam to the alias you can simply deactivate it or delete it all
          together!
        </p>
        <p class="mb-4">
          Try it out now by sending an email to <b>first@{{ subdomain }}</b> and then refresh this
          page.
        </p>
      </div>
      <div v-if="domain">
        <p v-if="!subdomain" class="mb-4 text-md text-grey-700 dark:text-grey-200">
          There is one way to create new aliases.
        </p>
        <h3 class="mb-2 text-lg text-indigo-800 dark:text-indigo-400 font-semibold">
          Create a unique random alias
        </h3>
        <p class="mb-2 text-grey-700 dark:text-grey-200">
          You can click the button above to create a random alias that will look something like
          this:
        </p>
        <p class="mb-2 text-grey-700 dark:text-grey-200">
          <b>x481n904@{{ domain }}</b>
        </p>
        <p class="text-grey-700 dark:text-grey-200">
          This is useful if you do not wish to include your username in the email as a potential
          link between aliases.
        </p>
      </div>
      <div class="mt-4">
        <button
          @click="createAliasModalOpen = true"
          type="button"
          class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 text-sm font-medium shadow-sm focus:outline-none"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Create Your First Alias
        </button>
      </div>
    </div>

    <Modal :open="createAliasModalOpen" @close="createAliasModalOpen = false">
      <template v-slot:title> Create new alias </template>
      <template v-slot:content>
        <p v-if="subdomain" class="mt-4 text-grey-700 dark:text-grey-200">
          Other aliases e.g. alias@{{ subdomain }} can also be created automatically when they
          receive their first email.
        </p>
        <label
          for="alias_domain"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
        >
          Alias Domain
        </label>
        <div class="block relative w-full mb-4">
          <select
            v-model="createAliasDomain"
            id="alias_domain"
            class="block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6"
            required
          >
            <option
              v-for="domainOption in domainOptions"
              :key="domainOption"
              :value="domainOption"
              class="dark:bg-grey-900"
            >
              {{ domainOption }}
            </option>
          </select>
        </div>

        <label
          for="alias_format"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm mt-4 mb-2"
        >
          Alias Format
        </label>
        <div class="block relative w-full mb-4">
          <select
            v-model="createAliasFormat"
            id="alias_format"
            class="block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6"
            required
          >
            <option
              v-for="formatOption in aliasFormatOptions"
              :key="formatOption.value"
              :value="formatOption.value"
              class="dark:bg-grey-900"
            >
              {{ formatOption.label }}
            </option>
          </select>
        </div>

        <div v-if="createAliasFormat === 'custom'">
          <label
            for="alias_local_part"
            class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
          >
            Alias Local Part
          </label>
          <p v-show="errors.createAliasLocalPart" class="mb-3 text-red-500 text-sm">
            {{ errors.createAliasLocalPart }}
          </p>
          <input
            v-model="createAliasLocalPart"
            id="alias_local_part"
            type="text"
            class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:text-white dark:bg-white/5"
            :class="errors.createAliasLocalPart ? 'border-red-500' : ''"
            placeholder="Enter local part..."
            autofocus
          />
        </div>

        <label
          for="alias_description"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
        >
          Description
        </label>
        <p v-show="errors.createAliasDescription" class="mb-3 text-red-500 text-sm">
          {{ errors.createAliasDescription }}
        </p>
        <input
          v-model="createAliasDescription"
          id="alias_description"
          type="text"
          class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:text-white dark:bg-white/5"
          :class="errors.createAliasDescription ? 'ring-red-500' : ''"
          placeholder="Enter description (optional)..."
          autofocus
        />

        <label
          for="alias_recipient_ids"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
        >
          Recipients
        </label>
        <p v-show="errors.createAliasRecipientIds" class="mb-3 text-red-500 text-sm">
          {{ errors.createAliasRecipientIds }}
        </p>
        <multiselect
          id="alias_recipient_ids"
          v-model="createAliasRecipientIds"
          mode="tags"
          value-prop="id"
          :options="recipientOptions"
          :multiple="true"
          :close-on-select="true"
          :clear-on-select="false"
          :searchable="true"
          :max="10"
          class="p-0"
          placeholder="Select recipient(s) (optional)..."
          label="email"
          track-by="email"
        >
        </multiselect>

        <div v-if="aliasGroups.length > 0" class="mt-4">
          <label
            for="alias_group_id"
            class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
          >
            Group
          </label>
          <select
            v-model="createAliasGroupId"
            id="alias_group_id"
            class="block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6"
          >
            <option :value="null" class="dark:bg-grey-900">No group</option>
            <option
              v-for="g in aliasGroups"
              :key="g.id"
              :value="g.id"
              class="dark:bg-grey-900"
            >{{ g.name }}</option>
          </select>
        </div>

        <div
          class="mt-6 rounded-md border border-grey-200 dark:border-grey-700 p-4"
          v-if="$page.props.user?.can_use_ghost_inbox"
        >
          <label
            class="flex items-center gap-2 cursor-pointer"
            :class="$page.props.user?.has_ghost_vault ? '' : 'opacity-60'"
          >
            <input
              type="checkbox"
              v-model="createAliasGhostMode"
              :disabled="!$page.props.user?.has_ghost_vault"
              class="rounded border-grey-300 text-indigo-600 focus:ring-indigo-500"
            />
            <span class="font-medium text-grey-800 dark:text-grey-100 text-sm">
              Ghost mode — store in encrypted inbox, don't forward
            </span>
            <span
              class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300"
            >Pro</span>
          </label>
          <p
            v-if="!$page.props.user?.has_ghost_vault"
            class="text-xs text-amber-600 dark:text-amber-400 mt-2"
          >
            Set up your vault in
            <Link :href="route('settings.ghost_inbox')" class="underline">Settings → Ghost Inbox</Link>
            first.
          </p>
        </div>

        <div class="mt-6 rounded-md border border-grey-200 dark:border-grey-700 p-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              v-model="createAliasIsBurner"
              class="rounded border-grey-300 text-indigo-600 focus:ring-indigo-500"
            />
            <span class="font-medium text-grey-800 dark:text-grey-100 text-sm">
              Make this a burner alias
            </span>
            <span
              class="inline-flex items-center rounded-full bg-cyan-100 dark:bg-cyan-900/40 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-300"
            >New</span>
          </label>

          <div v-if="createAliasIsBurner" class="mt-4 space-y-4">
            <div>
              <p class="text-xs font-medium text-grey-600 dark:text-grey-300 mb-2">
                Auto-deactivate after
              </p>
              <div class="flex flex-wrap gap-2">
                <button
                  type="button"
                  v-for="opt in [
                    { value: 'none', label: 'No time limit' },
                    { value: '1h', label: '1 hour' },
                    { value: '24h', label: '24 hours' },
                    { value: '3d', label: '3 days' },
                    { value: '7d', label: '7 days' },
                    { value: '30d', label: '30 days' },
                  ]"
                  :key="opt.value"
                  @click="createAliasExpiryPreset = opt.value"
                  :class="[
                    'rounded-full px-3 py-1 text-xs font-medium border',
                    createAliasExpiryPreset === opt.value
                      ? 'bg-indigo-600 text-white border-indigo-600'
                      : 'bg-white dark:bg-grey-800 text-grey-700 dark:text-grey-200 border-grey-200 dark:border-grey-700 hover:bg-grey-50 dark:hover:bg-grey-700',
                  ]"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <div>
              <p class="text-xs font-medium text-grey-600 dark:text-grey-300 mb-2">
                Or expire after N emails
              </p>
              <div class="flex flex-wrap gap-2">
                <button
                  type="button"
                  v-for="opt in [
                    { value: 'none', label: 'No email limit' },
                    { value: '1', label: '1 email' },
                    { value: '3', label: '3 emails' },
                    { value: '10', label: '10 emails' },
                  ]"
                  :key="opt.value"
                  @click="createAliasMaxEmailsPreset = opt.value"
                  :class="[
                    'rounded-full px-3 py-1 text-xs font-medium border',
                    createAliasMaxEmailsPreset === opt.value
                      ? 'bg-indigo-600 text-white border-indigo-600'
                      : 'bg-white dark:bg-grey-800 text-grey-700 dark:text-grey-200 border-grey-200 dark:border-grey-700 hover:bg-grey-50 dark:hover:bg-grey-700',
                  ]"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <div>
              <p class="text-xs font-medium text-grey-600 dark:text-grey-300 mb-2">
                When expired
              </p>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm text-grey-700 dark:text-grey-200">
                  <input
                    type="radio"
                    v-model="createAliasOnExpiry"
                    value="discard"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  Silently discard
                </label>
                <label class="flex items-center gap-2 text-sm text-grey-700 dark:text-grey-200">
                  <input
                    type="radio"
                    v-model="createAliasOnExpiry"
                    value="bounce"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  Bounce back to sender
                </label>
              </div>
            </div>

            <p
              v-if="createAliasIsBurner && createAliasExpiryPreset === 'none' && createAliasMaxEmailsPreset === 'none'"
              class="text-xs text-amber-600 dark:text-amber-400"
            >
              Pick at least one limit — otherwise this alias won't expire.
            </p>
          </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            @click="createNewAlias"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="createAliasLoading"
          >
            Create Alias
            <loader v-if="createAliasLoading" />
          </button>
          <button
            @click="createAliasModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="editAliasRecipientsModalOpen" @close="closeAliasRecipientsModal">
      <template v-slot:title> Update Alias Recipients </template>
      <template v-slot:content>
        <p class="my-4 text-grey-700 dark:text-grey-200">
          Select the recipients for this alias. You can choose multiple recipients. Leave it empty
          if you would like to use the default recipient.
        </p>
        <multiselect
          v-model="aliasRecipientsToEdit"
          mode="tags"
          value-prop="id"
          :options="recipientOptions"
          :multiple="true"
          :close-on-select="true"
          :clear-on-select="false"
          :searchable="true"
          :max="10"
          class="p-0"
          placeholder="Select recipient(s)"
          label="email"
          track-by="email"
        >
        </multiselect>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="editAliasRecipients()"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="editAliasRecipientsLoading"
          >
            Update Recipients
            <loader v-if="editAliasRecipientsLoading" />
          </button>
          <button
            @click="closeAliasRecipientsModal()"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="bulkEditAliasRecipientsModalOpen" @close="closeBulkAliasRecipientsModal()">
      <template v-slot:title> Update Recipients for Aliases </template>
      <template v-slot:content>
        <p class="my-4 text-grey-700 dark:text-grey-200">
          Select the recipients for these <b>{{ selectedRowIds.length }}</b> aliases. You can choose
          multiple recipients. Leave it empty if you would like to use the default recipient.
        </p>
        <multiselect
          v-model="aliasRecipientsToEdit"
          mode="tags"
          value-prop="id"
          :options="recipientOptions"
          :multiple="true"
          :close-on-select="true"
          :clear-on-select="false"
          :searchable="true"
          :max="10"
          class="p-0"
          placeholder="Select recipient(s)"
          label="email"
          track-by="email"
        >
        </multiselect>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="bulkEditAliasRecipients()"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="bulkEditAliasRecipientsLoading"
          >
            Update Recipients
            <loader v-if="bulkEditAliasRecipientsLoading" />
          </button>
          <button
            @click="closeBulkAliasRecipientsModal()"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="bulkGroupModalOpen" @close="bulkGroupModalOpen = false">
      <template v-slot:title>Move to group</template>
      <template v-slot:content>
        <p class="my-4 text-grey-700 dark:text-grey-200">
          Move the <b>{{ selectedRowIds.length }}</b> selected {{ selectedRowIds.length === 1 ? 'alias' : 'aliases' }} into a group.
        </p>
        <select
          v-model="bulkGroupTarget"
          class="w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset ring-grey-300 focus:z-10 focus:ring-2 focus:ring-inset sm:text-base"
        >
          <option :value="null" class="dark:bg-grey-900">No group (remove from any current group)</option>
          <option
            v-for="g in aliasGroups"
            :key="g.id"
            :value="g.id"
            class="dark:bg-grey-900"
          >{{ g.name }}</option>
        </select>
        <div class="mt-6 flex gap-3">
          <button
            type="button"
            @click="bulkMoveToGroup()"
            :disabled="bulkGroupLoading"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 rounded disabled:cursor-not-allowed"
          >
            Move <loader v-if="bulkGroupLoading" />
          </button>
          <button
            @click="bulkGroupModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 border border-grey-100 rounded"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="restoreAliasModalOpen" @close="closeRestoreModal">
      <template v-slot:title> Restore alias </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to restore <b class="break-words">{{ aliasToRestore.email }}</b
          >? Once restored <b class="break-words">{{ aliasToRestore.email }}</b> will be
          <b>able to forward emails again</b>.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="restoreAlias(aliasToRestore.id)"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="restoreAliasLoading"
          >
            Restore alias
            <loader v-if="restoreAliasLoading" />
          </button>
          <button
            @click="closeRestoreModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="bulkRestoreAliasModalOpen" @close="bulkRestoreAliasModalOpen = false">
      <template v-slot:title> Restore aliases </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to restore these
          <b>{{ selectedAliasesToRestore.length }}</b> aliases? Once restored they will be
          <b>able to forward emails again</b>.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="bulkRestoreAlias()"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="bulkRestoreAliasLoading"
          >
            Restore aliases
            <loader v-if="bulkRestoreAliasLoading" />
          </button>
          <button
            @click="bulkRestoreAliasModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="deleteAliasModalOpen" @close="closeDeleteModal">
      <template v-slot:title> Delete alias </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to delete <b class="break-words">{{ aliasToDelete.email }}</b
          >? You can restore it if you later change your mind. Once deleted,
          <b class="break-words">{{ aliasToDelete.email }}</b> will
          <b>reject any emails sent to it</b>.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="deleteAlias(aliasToDelete.id)"
            class="px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="deleteAliasLoading"
          >
            Delete alias
            <loader v-if="deleteAliasLoading" />
          </button>
          <button
            @click="closeDeleteModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="bulkDeleteAliasModalOpen" @close="bulkDeleteAliasModalOpen = false">
      <template v-slot:title> Delete aliases </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to delete these
          <b>{{ selectedAliasesToDelete.length }}</b> aliases? You can restore them if you later
          change your mind. Once deleted, these aliases will <b>reject any emails sent to them</b>.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="bulkDeleteAlias()"
            class="px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="bulkDeleteAliasLoading"
          >
            Delete aliases
            <loader v-if="bulkDeleteAliasLoading" />
          </button>
          <button
            @click="bulkDeleteAliasModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="forgetAliasModalOpen" @close="closeForgetModal">
      <template v-slot:title> Forget alias </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to forget <b class="break-words">{{ aliasToForget.email }}</b
          >? Forgetting an alias will disassociate it from your account.
        </p>
        <p
          v-if="sharedDomains.includes(aliasToForget.domain)"
          class="mt-4 text-grey-700 dark:text-grey-200"
        >
          <b>Note:</b> This alias uses a shared domain so it can
          <b>never be restored or used again</b> so make sure you are certain. Once forgotten,
          <b class="break-words">{{ aliasToForget.email }}</b> will reject any emails sent to it.
        </p>
        <p v-else class="mt-4 text-grey-700 dark:text-grey-200">
          <b>Note:</b> This is a standard alias so it
          <b>can be created again in the future</b> since it will be as if it never existed in the
          database. Once forgotten, if someone sends an email to this alias and you have
          <b>catch-all enabled</b> for your
          <b
            >"{{
              aliasToForget.aliasable_type === 'App\\Models\\Username'
                ? aliasToForget.domain.split('.')[0] + '" username'
                : aliasToForget.domain + '" domain'
            }}</b
          >
          then it will be created automatically again. If you would like this alias to reject any
          messages sent to it then you should delete it instead.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="forgetAlias(aliasToForget.id)"
            class="px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="forgetAliasLoading"
          >
            Forget alias
            <loader v-if="forgetAliasLoading" />
          </button>
          <button
            @click="closeForgetModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="bulkForgetAliasModalOpen" @close="bulkForgetAliasModalOpen = false">
      <template v-slot:title> Forget aliases </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Are you sure you want to forget these
          <b>{{ selectedRowIds.length }}</b> aliases? Forgetting these aliases will disassociate
          them from your account.
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          <b>Note:</b> If the alias uses a shared domain then it can <b>never be restored</b> or
          used again so make sure you are certain. If it is a standard alias then it can be created
          again since it will be as if it never existed.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="bulkForgetAlias()"
            class="px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="bulkForgetAliasLoading"
          >
            Forget aliases
            <loader v-if="bulkForgetAliasLoading" />
          </button>
          <button
            @click="bulkForgetAliasModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Cancel
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="sendFromAliasModalOpen" @close="closeSendFromModal">
      <template v-slot:title> Send from alias </template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          Use this to automatically create the correct address to send an email to in order to send
          an <b>email from this alias</b>.
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          To send from an alias you must send the email from a <b>verified recipient</b> on your
          account.
        </p>
        <label
          for="send_from_alias"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
        >
          Alias to send from
        </label>
        <input
          v-model="aliasToSendFrom.email"
          id="send_from_alias"
          type="text"
          class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 bg-grey-50 dark:bg-white/5 dark:text-white"
          disabled
        />
        <label
          for="send_from_alias_destination"
          class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
        >
          To email destination
        </label>
        <p v-show="errors.sendFromAliasDestination" class="mb-3 text-red-500 text-sm">
          {{ errors.sendFromAliasDestination }}
        </p>
        <input
          v-model="sendFromAliasDestination"
          id="send_from_alias_destination"
          type="text"
          class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 dark:text-white"
          :class="errors.sendFromAliasDestination ? 'ring-red-500' : ''"
          placeholder="Enter email..."
          autofocus
        />
        <div v-if="sendFromAliasEmailToSendTo">
          <p
            for="alias_domain"
            class="block font-medium leading-6 text-grey-600 dark:text-white text-sm my-2"
          >
            Send your message to this email
          </p>
          <div
            @click="(clipboard(sendFromAliasEmailToSendTo), setSendFromAliasCopied())"
            class="flex items-center justify-between cursor-pointer text-sm font-medium border-t-4 rounded-sm text-green-800 border-green-600 bg-green-100 p-2 mb-3"
            role="alert"
          >
            <span>
              {{ sendFromAliasEmailToSendTo }}
            </span>
            <svg
              v-if="sendFromAliasCopied"
              viewBox="0 0 24 24"
              width="20"
              height="20"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="9 11 12 14 22 4"></polyline>
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            <svg
              v-else
              viewBox="0 0 24 24"
              width="20"
              height="20"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
          </div>
          <a
            :href="'mailto:' + sendFromAliasEmailToSendTo"
            class="flex items-center justify-between cursor-pointer text-sm border-t-4 rounded-sm text-green-800 border-green-600 bg-green-100 p-2 mb-4"
            role="alert"
            title="Click To Open Mail Application"
          >
            Click to open mail application
          </a>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="displaySendFromAddress(aliasToSendFrom)"
            class="px-4 py-3 text-white font-semibold bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="sendFromAliasLoading"
          >
            Show address
            <loader v-if="sendFromAliasLoading" />
          </button>
          <button
            @click="closeSendFromModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
    <Modal :open="newAliasModalOpen" @close="newAliasModalOpen = false">
      <template v-slot:title> Your New Alias is: </template>
      <template v-slot:content>
        <p class="my-8 text-grey-700 dark:text-grey-200">
          <button
            class="text-grey-400 tooltip outline-none"
            data-tippy-content="Click to copy"
            @click="clipboard(getNewAliasEmail())"
          >
            <span class="font-semibold text-indigo-800 dark:text-indigo-400">{{
              newAliasExtension ? `${newAliasLocalPart}+${newAliasExtension}` : newAliasLocalPart
            }}</span
            ><span class="font-semibold text-grey-500">@{{ newAliasDomain }}</span>
          </button>
        </p>

        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            @click="clipboard(getNewAliasEmail())"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Copy
          </button>
          <button
            @click="newAliasModalOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
    <Modal :open="moreInfoOpen" @close="moreInfoOpen = false">
      <template v-slot:title> More information </template>
      <template v-slot:content>
        <p class="mt-4 text-md text-grey-700 dark:text-grey-200">
          Aliases come under two different categories.
        </p>

        <p class="mt-4 text-grey-700 dark:text-grey-200">
          <b>Standard Aliases</b> - Standard aliases use a domain that is unique only to you, all
          aliases for your custom domains are classed as standard aliases. Standard aliases can be
          created automatically when they receive their first email (if catch-all is enabled for the
          domain). If you signed up with a username of mrunknown and gave out the following alias -
          hello@mrunknown.mailflusher.com then this would be a standard alias.
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          <b>Shared Domain Aliases</b> - A shared domain alias is any alias that has a domain name
          that is also shared with other users. Aliases with shared domain names must be
          pre-generated and cannot be created on-the-fly like standard aliases.
        </p>

        <div class="mt-6 flex flex-col sm:flex-row">
          <button
            @click="moreInfoOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import Modal from '../../Components/Modal.vue'
import Toggle from '../../Components/Toggle.vue'
import GroupColorChip from '../../Components/GroupColorChip.vue'
import { roundArrow } from 'tippy.js'
import tippy from 'tippy.js'
import { VueGoodTable } from 'vue-good-table-next'
import Multiselect from '@vueform/multiselect'
import { notify } from '@kyvg/vue3-notification'
import {
  Listbox,
  ListboxButton,
  ListboxLabel,
  ListboxOption,
  ListboxOptions,
} from '@headlessui/vue'
import {
  InformationCircleIcon,
  AtSymbolIcon,
  BarsArrowDownIcon,
  BarsArrowUpIcon,
  EnvelopeIcon,
} from '@heroicons/vue/24/outline'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  CheckIcon,
  PlusIcon,
} from '@heroicons/vue/20/solid'

const props = defineProps({
  initialRows: {
    type: Object,
    required: true,
  },
  recipientOptions: {
    type: Array,
    required: true,
  },
  domain: {
    type: String,
    required: false,
  },
  subdomain: {
    type: String,
    required: false,
  },
  domainOptions: {
    type: Array,
    required: true,
  },
  defaultAliasDomain: {
    type: String,
    required: true,
  },
  defaultAliasFormat: {
    type: String,
    required: true,
  },
  search: {
    type: String,
  },
  initialPageSize: {
    type: Number,
    required: true,
  },
  sort: {
    type: String,
  },
  sortDirection: {
    type: String,
  },
  currentAliasStatus: {
    type: String,
  },
  sharedDomains: {
    type: Array,
    required: true,
  },
  pinnedFilter: {
    type: String,
    default: null,
  },
  deletedAliasCount: {
    type: Number,
    default: 0,
  },
  aliasGroups: {
    type: Array,
    default: () => [],
  },
  activeGroupFilter: {
    type: String,
    default: null,
  },
})

const activeGroup = computed(() => {
  if (!props.activeGroupFilter) return null
  return props.aliasGroups.find(g => g.id === props.activeGroupFilter) || null
})

const clearGroupFilterHref = computed(() =>
  route('aliases.index', _.omit(route().params, ['group']))
)

const rows = ref(props.initialRows.data)

const selectedRowIds = ref([])
const selectedRows = computed(() =>
  _.filter(rows.value, row => selectedRowIds.value.includes(row.id)),
)
const checked = ref(false)
const indeterminate = computed(
  () => selectedRows.value.length > 0 && selectedRows.value.length < rows.value.length,
)

const selectedAliasesToDelete = computed(() =>
  _.filter(selectedRows.value, row => row.deleted_at === null),
)
const selectedAliasesToRestore = computed(() =>
  _.filter(selectedRows.value, row => row.deleted_at !== null),
)

const links = ref(props.initialRows.links.slice(1, -1))

const aliasIdToEdit = ref('')
const aliasDescriptionToEdit = ref('')
const aliasToDelete = ref({})
const aliasToForget = ref({})
const aliasToSendFrom = ref({})
const sendFromAliasDestination = ref('')
const sendFromAliasEmailToSendTo = ref('')
const sendFromAliasCopied = ref(false)
const aliasToRestore = ref({})
const deleteAliasLoading = ref(false)
const forgetAliasLoading = ref(false)
const deleteAliasModalOpen = ref(false)
const forgetAliasModalOpen = ref(false)
const sendFromAliasLoading = ref(false)
const sendFromAliasModalOpen = ref(false)
const restoreAliasLoading = ref(false)
const restoreAliasModalOpen = ref(false)
const editAliasRecipientsLoading = ref(false)
const editAliasRecipientsModalOpen = ref(false)
const createAliasModalOpen = ref(false)
const createAliasGroupId = ref(null)
const createAliasGhostMode = ref(false)
const createAliasIsBurner = ref(false)
const createAliasExpiryPreset = ref('none') // 'none' | '1h' | '24h' | '3d' | '7d' | '30d'
const createAliasMaxEmailsPreset = ref('none') // 'none' | '1' | '3' | '10'
const createAliasOnExpiry = ref('discard') // 'discard' | 'bounce'

const expiryPresetToDate = preset => {
  const now = new Date()
  switch (preset) {
    case '1h':
      now.setHours(now.getHours() + 1)
      break
    case '24h':
      now.setDate(now.getDate() + 1)
      break
    case '3d':
      now.setDate(now.getDate() + 3)
      break
    case '7d':
      now.setDate(now.getDate() + 7)
      break
    case '30d':
      now.setDate(now.getDate() + 30)
      break
    default:
      return null
  }
  return now.toISOString()
}

const maxEmailsFromPreset = preset => {
  const n = parseInt(preset, 10)
  return isNaN(n) ? null : n
}

const newAliasLocalPart = ref('')
const newAliasExtension = ref('')
const newAliasDomain = ref('')
const newAliasModalOpen = ref(false)
const createAliasLoading = ref(false)
const createAliasDomain = ref(props.defaultAliasDomain)
const createAliasLocalPart = ref('')
const createAliasDescription = ref('')
const createAliasRecipientIds = ref([])
const createAliasFormat = ref(props.defaultAliasFormat)
const moreInfoOpen = ref(false)
const recipientsAliasToEdit = ref({})
const aliasRecipientsToEdit = ref([])
const tippyInstance = ref(null)
const errors = ref({})
const bulkActivateAliasLoading = ref(false)
const bulkDeactivateAliasLoading = ref(false)
const bulkPinAliasLoading = ref(false)
const bulkUnpinAliasLoading = ref(false)
const bulkEditAliasRecipientsLoading = ref(false)
const bulkEditAliasRecipientsModalOpen = ref(false)
const bulkDeleteAliasLoading = ref(false)
const bulkDeleteAliasModalOpen = ref(false)
const bulkForgetAliasLoading = ref(false)
const bulkForgetAliasModalOpen = ref(false)
const bulkRestoreAliasLoading = ref(false)
const bulkGroupModalOpen = ref(false)
const bulkGroupTarget = ref(null)
const bulkGroupLoading = ref(false)

const bulkMoveToGroup = () => {
  bulkGroupLoading.value = true
  axios
    .post('/api/v1/aliases/group/bulk', {
      ids: selectedRowIds.value,
      alias_group_id: bulkGroupTarget.value,
    })
    .then(() => {
      router.reload({
        only: ['initialRows', 'aliasGroups', 'activeGroupFilter'],
        onSuccess: page => {
          rows.value = page.props.initialRows.data
          selectedRowIds.value = []
          bulkGroupModalOpen.value = false
          bulkGroupLoading.value = false
          bulkGroupTarget.value = null
          successMessage('Aliases moved')
        },
      })
    })
    .catch(error => {
      bulkGroupLoading.value = false
      errorMessage(error.response?.data?.message || 'Could not move aliases.')
    })
}
const bulkRestoreAliasModalOpen = ref(false)
const changeSortDirLoading = ref(false)
const pageSize = ref(props.initialPageSize)
const updatePageSizeLoading = ref(false)

const pageSizeOptions = [25, 50, 100]

const displayOptions = [
  {
    value: 'all',
    label: 'All',
    params: {
      deleted: 'with',
    },
    omit: ['page', 'active'],
  },
  {
    value: 'active_inactive',
    label: 'Active and Inactive',
    params: {
      active: 'both',
    },
    omit: ['page', 'deleted'],
  },
  {
    value: 'active',
    label: 'Active only',
    params: {
      active: 'true',
    },
    omit: ['page', 'deleted'],
  },
  {
    value: 'inactive',
    label: 'Inactive only',
    params: {
      active: 'false',
    },
    omit: ['page', 'deleted'],
  },
  {
    value: 'deleted',
    label: 'Deleted only',
    params: {
      deleted: 'only',
    },
    omit: ['page', 'active'],
  },
]

const showAliasStatus = ref(_.find(displayOptions, ['value', props.currentAliasStatus]))

const pinnedFilterOptions = [
  { value: 'all', label: 'All' },
  { value: 'pinned', label: 'Pinned' },
  { value: 'unpinned', label: 'Unpinned' },
]
const getPinnedFilterOption = pinnedFilter => {
  const value = pinnedFilter == null ? 'all' : pinnedFilter === 'true' ? 'pinned' : 'unpinned'
  return _.find(pinnedFilterOptions, ['value', value]) ?? pinnedFilterOptions[0]
}
const selectedPinnedFilter = ref(getPinnedFilterOption(props.pinnedFilter))

const sortOptions = [
  {
    value: 'active',
    label: 'Active',
  },
  {
    value: 'email',
    label: 'Alias',
  },
  {
    value: 'created_at',
    label: 'Created At',
  },
  {
    value: 'deleted_at',
    label: 'Deleted At',
  },
  {
    value: 'domain',
    label: 'Domain',
  },
  {
    value: 'emails_blocked',
    label: 'Emails Blocked',
  },
  {
    value: 'emails_forwarded',
    label: 'Emails Forwarded',
  },
  {
    value: 'emails_replied',
    label: 'Emails Replied',
  },
  {
    value: 'emails_sent',
    label: 'Emails Sent',
  },
  {
    value: 'last_blocked',
    label: 'Last Blocked At',
  },
  {
    value: 'last_forwarded',
    label: 'Last Forwarded At',
  },
  {
    value: 'last_replied',
    label: 'Last Replied At',
  },
  {
    value: 'last_sent',
    label: 'Last Sent At',
  },
  {
    value: 'last_used',
    label: 'Last Used At',
  },
  {
    value: 'updated_at',
    label: 'Updated At',
  },
]
const currentSort = ref(_.find(sortOptions, ['value', props.sort]))

const aliasFormatOptions = [
  {
    value: 'random_characters',
    label: 'Random Characters',
  },
  {
    value: 'uuid',
    label: 'UUID',
  },
  {
    value: 'random_words',
    label: 'Random Words',
  },
  {
    value: 'random_male_name',
    label: 'Random Male Name',
  },
  {
    value: 'random_female_name',
    label: 'Random Female Name',
  },
  {
    value: 'random_noun',
    label: 'Random Noun',
  },
  {
    value: 'custom',
    label: 'Custom',
  },
]
const columns = [
  {
    label: '',
    field: 'select',
  },
  {
    label: 'Created',
    field: 'created_at',
    thClass: 'mobile-hide',
    tdClass: 'mobile-hide',
  },
  {
    label: 'Alias',
    field: 'email',
  },
  {
    label: 'Recipients',
    field: 'recipients',
    tdClass: 'text-center',
  },
  {
    label: 'Forwards/Blocks',
    field: 'emails_forwarded',
    type: 'number',
    thClass: 'mobile-hide',
    tdClass: 'text-center mobile-hide',
  },
  {
    label: 'Replies/Sends',
    field: 'emails_replied',
    type: 'number',
    thClass: 'mobile-hide',
    tdClass: 'text-center mobile-hide',
  },
  {
    label: 'Active',
    field: 'active',
    type: 'boolean',
  },
  {
    label: '',
    field: 'actions',
  },
]

watch(
  () => showAliasStatus,
  function (status) {
    let params = Object.assign(route().params, status.value.params)

    router.visit(route('aliases.index', _.omit(params, status.value.omit)), {
      only: [
        'initialRows',
        'search',
        'sort',
        'sortDirection',
        'currentAliasStatus',
        'pinnedFilter',
      ],
    })
  },
  { deep: true },
)

watch(
  () => currentSort,
  function (sort) {
    let params = Object.assign(route().params, {
      sort: props.sortDirection === 'desc' ? '-' + sort.value.value : sort.value.value,
    })

    router.visit(route('aliases.index', _.omit(params, ['page'])), {
      only: [
        'initialRows',
        'search',
        'sort',
        'sortDirection',
        'currentAliasStatus',
        'pinnedFilter',
      ],
    })
  },
  { deep: true },
)

const applyPinnedFilter = value => {
  const params = { ...route().params }
  params.pinned = value === 'all' ? 'all' : value === 'pinned' ? 'true' : 'false'
  router.visit(route('aliases.index', params), {
    only: ['initialRows', 'search', 'sort', 'sortDirection', 'currentAliasStatus', 'pinnedFilter'],
  })
}
watch(
  () => props.pinnedFilter,
  val => {
    selectedPinnedFilter.value = getPinnedFilterOption(val)
  },
)
watch(
  selectedPinnedFilter,
  (newOpt, oldOpt) => {
    if (!oldOpt || newOpt.value === oldOpt.value) return
    const currentValue =
      props.pinnedFilter == null ? 'all' : props.pinnedFilter === 'true' ? 'pinned' : 'unpinned'
    if (newOpt.value === currentValue) return
    applyPinnedFilter(newOpt.value)
  },
  { deep: true },
)

onMounted(() => {
  debounceToolips()
})

const createNewAlias = () => {
  errors.value = {}

  // Validate alias local part
  if (createAliasFormat.value === 'custom' && !validLocalPart(createAliasLocalPart.value)) {
    return (errors.value.createAliasLocalPart = 'Valid local part required')
  }

  if (createAliasDescription.value.length > 200) {
    return (errors.value.createAliasDescription = 'Description cannot exceed 200 characters')
  }

  createAliasLoading.value = true

  const payload = {
    domain: createAliasDomain.value,
    local_part: createAliasLocalPart.value,
    description: createAliasDescription.value,
    format: createAliasFormat.value,
    recipient_ids: createAliasRecipientIds.value,
  }

  if (createAliasIsBurner.value) {
    const expiresAt = expiryPresetToDate(createAliasExpiryPreset.value)
    const maxEmails = maxEmailsFromPreset(createAliasMaxEmailsPreset.value)
    if (expiresAt) payload.expires_at = expiresAt
    if (maxEmails) payload.max_emails = maxEmails
    payload.on_expiry = createAliasOnExpiry.value
  }

  if (createAliasGhostMode.value) {
    payload.ghost_mode = true
  }

  if (createAliasGroupId.value) {
    payload.alias_group_id = createAliasGroupId.value
  }

  axios
    .post('/api/v1/aliases', JSON.stringify(payload), {
      headers: { 'Content-Type': 'application/json' },
    })
    .then(({ data }) => {
      // Show active/inactive
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          rows.value = page.props.initialRows.data
          createAliasLoading.value = false
          createAliasLocalPart.value = ''
          createAliasDescription.value = ''
          createAliasRecipientIds.value = []
          createAliasIsBurner.value = false
          createAliasExpiryPreset.value = 'none'
          createAliasMaxEmailsPreset.value = 'none'
          createAliasOnExpiry.value = 'discard'
          createAliasGhostMode.value = false
          createAliasGroupId.value = null
          createAliasModalOpen.value = false
          newAliasLocalPart.value = data.data.local_part
          newAliasExtension.value = data.data.extension
          newAliasDomain.value = data.data.domain
          newAliasModalOpen.value = true
          debounceToolips()
          successMessage('New alias created successfully')
        },
      })
    })
    .catch(error => {
      createAliasLoading.value = false
      if ([429, 403].includes(error.response.status)) {
        errorMessage(error.response.data)
      } else if (error.response.status === 422) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const editAliasDescription = alias => {
  if (aliasDescriptionToEdit.value.length > 200) {
    return errorMessage('Description cannot be more than 200 characters')
  }

  axios
    .patch(
      `/api/v1/aliases/${alias.id}`,
      JSON.stringify({
        description: aliasDescriptionToEdit.value,
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      alias.description = aliasDescriptionToEdit.value
      aliasIdToEdit.value = ''
      aliasDescriptionToEdit.value = ''
      successMessage('Alias description updated')
    })
    .catch(error => {
      aliasIdToEdit.value = ''
      aliasDescriptionToEdit.value = ''
      errorMessage()
    })
}

const editAliasRecipients = () => {
  editAliasRecipientsLoading.value = true

  axios
    .post(
      '/api/v1/alias-recipients',
      JSON.stringify({
        alias_id: recipientsAliasToEdit.value.id,
        recipient_ids: aliasRecipientsToEdit.value,
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      let alias = _.find(rows.value, ['id', recipientsAliasToEdit.value.id])

      // JSON required to fix failed to execute 'replaceState' on 'History' error
      alias.recipients = JSON.parse(
        JSON.stringify(
          _.filter(props.recipientOptions, recipient =>
            aliasRecipientsToEdit.value.includes(recipient.id),
          ),
        ),
      )

      editAliasRecipientsLoading.value = false
      closeAliasRecipientsModal()
      successMessage('Alias recipients updated')
    })
    .catch(error => {
      editAliasRecipientsLoading.value = false
      closeAliasRecipientsModal()
      errorMessage()
    })
}

const bulkEditAliasRecipients = () => {
  bulkEditAliasRecipientsLoading.value = true
  // No need to filter
  let selectedAliasesToEditRecipients = selectedRows.value

  axios
    .post(
      '/api/v1/aliases/recipients/bulk',
      JSON.stringify({
        ids: selectedAliasesToEditRecipients.map(a => a.id),
        recipient_ids: aliasRecipientsToEdit.value,
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      _.each(selectedAliasesToEditRecipients, alias => {
        // JSON required to fix failed to execute 'replaceState' on 'History' error
        alias.recipients = JSON.parse(
          JSON.stringify(
            _.filter(props.recipientOptions, recipient =>
              aliasRecipientsToEdit.value.includes(recipient.id),
            ),
          ),
        )
      })

      bulkEditAliasRecipientsLoading.value = false
      closeBulkAliasRecipientsModal()
      successMessage(response.data.message)
    })
    .catch(error => {
      bulkEditAliasRecipientsLoading.value = false
      closeBulkAliasRecipientsModal()
      errorMessage()
    })
}

const activateAlias = alias => {
  axios
    .post(
      `/api/v1/active-aliases`,
      JSON.stringify({
        id: alias.id,
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      alias.active = true
      debounceToolips()
    })
    .catch(error => {
      alias.active = false
      if (error.response !== undefined) {
        errorMessage(error.response.data)
      } else {
        errorMessage()
      }
    })
}

const bulkActivateAlias = () => {
  bulkActivateAliasLoading.value = true
  // First filter selected rows to remove any that are already active or are currently deleted
  let selectedAliasesToActivate = _.filter(selectedRows.value, r => {
    return !r.active && r.deleted_at === null
  })

  axios
    .post(
      `/api/v1/aliases/activate/bulk`,
      JSON.stringify({
        ids: selectedAliasesToActivate.map(a => a.id),
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      _.each(selectedAliasesToActivate, r => {
        r.active = true
      })
      bulkActivateAliasLoading.value = false
      debounceToolips()
      successMessage(response.data.message)
    })
    .catch(error => {
      bulkActivateAliasLoading.value = false
      if (error.response.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response.data.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const deactivateAlias = alias => {
  axios
    .delete(`/api/v1/active-aliases/${alias.id}`)
    .then(response => {
      alias.active = false
    })
    .catch(error => {
      alias.active = true
      debounceToolips()
      if (error.response !== undefined) {
        errorMessage(error.response.data)
      } else {
        errorMessage()
      }
    })
}

const bulkDeactivateAlias = () => {
  bulkDeactivateAliasLoading.value = true
  // First filter selected rows to remove any that are already deactivated
  let selectedAliasesToDeactivate = _.filter(selectedRows.value, r => {
    return r.active
  })

  axios
    .post(
      `/api/v1/aliases/deactivate/bulk`,
      JSON.stringify({
        ids: selectedAliasesToDeactivate.map(a => a.id),
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      _.each(selectedAliasesToDeactivate, r => {
        r.active = false
      })
      bulkDeactivateAliasLoading.value = false
      debounceToolips()
      successMessage(response.data.message)
    })
    .catch(error => {
      bulkDeactivateAliasLoading.value = false
      if (error.response.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response.data.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const pinAlias = alias => {
  bulkPinAliasLoading.value = true

  axios
    .post('/api/v1/pinned-aliases', JSON.stringify({ id: alias.id }), {
      headers: { 'Content-Type': 'application/json' },
    })
    .then(() => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          bulkPinAliasLoading.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          debounceToolips()
          successMessage('Alias pinned')
        },
      })
    })
    .catch(error => {
      bulkPinAliasLoading.value = false
      if (error.response !== undefined) {
        errorMessage(error.response.data)
      } else {
        errorMessage()
      }
    })
}

const bulkPinAlias = () => {
  bulkPinAliasLoading.value = true
  let selectedAliasesToPin = _.filter(selectedRows.value, r => !r.pinned)

  axios
    .post(
      '/api/v1/aliases/pin/bulk',
      JSON.stringify({ ids: selectedAliasesToPin.map(a => a.id) }),
      { headers: { 'Content-Type': 'application/json' } },
    )
    .then(response => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          bulkPinAliasLoading.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          debounceToolips()
          successMessage(response.data.message)
        },
      })
    })
    .catch(error => {
      bulkPinAliasLoading.value = false
      if (error.response?.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response?.data?.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const unpinAlias = alias => {
  bulkUnpinAliasLoading.value = true

  axios
    .delete(`/api/v1/pinned-aliases/${alias.id}`)
    .then(() => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          bulkUnpinAliasLoading.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          successMessage('Alias unpinned')
        },
      })
    })
    .catch(error => {
      bulkUnpinAliasLoading.value = false
      if (error.response !== undefined) {
        errorMessage(error.response.data)
      } else {
        errorMessage()
      }
    })
}

const bulkUnpinAlias = () => {
  bulkUnpinAliasLoading.value = true
  let selectedAliasesToUnpin = _.filter(selectedRows.value, 'pinned')

  axios
    .post(
      '/api/v1/aliases/unpin/bulk',
      JSON.stringify({ ids: selectedAliasesToUnpin.map(a => a.id) }),
      { headers: { 'Content-Type': 'application/json' } },
    )
    .then(response => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          bulkUnpinAliasLoading.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          successMessage(response.data.message)
        },
      })
    })
    .catch(error => {
      bulkUnpinAliasLoading.value = false
      if (error.response?.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response?.data?.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const deleteAlias = id => {
  deleteAliasLoading.value = true

  axios
    .delete(`/api/v1/aliases/${id}`)
    .then(response => {
      // If showing deleted then set as deleted and inactive
      if (['all', 'deleted'].includes(props.currentAliasStatus)) {
        let alias = _.find(rows.value, ['id', id])

        alias.deleted_at = dayjs.utc().format()
        alias.active = false
        alias.recipients = []

        deleteAliasModalOpen.value = false
        deleteAliasLoading.value = false
        selectedRowIds.value = []
        debounceToolips()
        successMessage('Alias deleted successfully')
      } else {
        router.reload({
          only: [
            'initialRows',
            'search',
            'currentAliasStatus',
            'sort',
            'sortDirection',
            'pinnedFilter',
          ],
          onSuccess: page => {
            deleteAliasModalOpen.value = false
            deleteAliasLoading.value = false
            selectedRowIds.value = []
            rows.value = props.initialRows.data
            successMessage('Alias deleted successfully')
          },
        })
      }
    })
    .catch(error => {
      errorMessage()
      deleteAliasModalOpen.value = false
      deleteAliasLoading.value = false
    })
}

const bulkDeleteAlias = () => {
  bulkDeleteAliasLoading.value = true

  axios
    .post(
      `/api/v1/aliases/delete/bulk`,
      JSON.stringify({
        ids: selectedAliasesToDelete.value.map(a => a.id),
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      // If showing deleted then set as deleted and inactive
      if (['all', 'deleted'].includes(props.currentAliasStatus)) {
        _.each(selectedAliasesToDelete.value, r => {
          r.deleted_at = dayjs.utc().format()
          r.active = false
          r.recipients = []
        })
        bulkDeleteAliasLoading.value = false
        bulkDeleteAliasModalOpen.value = false
        selectedRowIds.value = []
        debounceToolips()
        successMessage(response.data.message)
      } else {
        router.reload({
          only: [
            'initialRows',
            'search',
            'currentAliasStatus',
            'sort',
            'sortDirection',
            'pinnedFilter',
          ],
          onSuccess: page => {
            bulkDeleteAliasLoading.value = false
            bulkDeleteAliasModalOpen.value = false
            selectedRowIds.value = []
            rows.value = props.initialRows.data
            successMessage(response.data.message)
          },
        })
      }
    })
    .catch(error => {
      bulkDeleteAliasLoading.value = false
      bulkDeleteAliasModalOpen.value = false
      if (error.response.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response.data.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const forgetAlias = id => {
  forgetAliasLoading.value = true

  axios
    .delete(`/api/v1/aliases/${id}/forget`)
    .then(response => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          forgetAliasModalOpen.value = false
          forgetAliasLoading.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          successMessage('Alias forgotten successfully')
        },
      })
    })
    .catch(error => {
      errorMessage()
      forgetAliasModalOpen.value = false
      forgetAliasLoading.value = false
    })
}

const bulkForgetAlias = () => {
  bulkForgetAliasLoading.value = true
  // No need to filter
  let selectedAliasesToForget = selectedRows.value

  axios
    .post(
      `/api/v1/aliases/forget/bulk`,
      JSON.stringify({
        ids: selectedAliasesToForget.map(a => a.id),
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      router.reload({
        only: [
          'initialRows',
          'search',
          'currentAliasStatus',
          'sort',
          'sortDirection',
          'pinnedFilter',
        ],
        onSuccess: page => {
          bulkForgetAliasLoading.value = false
          bulkForgetAliasModalOpen.value = false
          selectedRowIds.value = []
          rows.value = props.initialRows.data
          successMessage(response.data.message)
        },
      })
    })
    .catch(error => {
      bulkForgetAliasLoading.value = false
      bulkForgetAliasModalOpen.value = false
      if (error.response.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response.data.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const restoreAlias = id => {
  restoreAliasLoading.value = true

  axios
    .patch(`/api/v1/aliases/${id}/restore`, {
      headers: { 'Content-Type': 'application/json' },
    })
    .then(response => {
      // If showing only deleted then reload all aliases
      if (props.currentAliasStatus === 'deleted') {
        router.reload({
          only: [
            'initialRows',
            'search',
            'currentAliasStatus',
            'sort',
            'sortDirection',
            'pinnedFilter',
          ],
          onSuccess: page => {
            restoreAliasModalOpen.value = false
            restoreAliasLoading.value = false
            selectedRowIds.value = []
            rows.value = props.initialRows.data
            successMessage('Alias restored successfully')
          },
        })
      } else {
        let alias = _.find(rows.value, ['id', id])
        alias.deleted_at = null
        alias.active = true

        restoreAliasModalOpen.value = false
        restoreAliasLoading.value = false
        selectedRowIds.value = []
        successMessage('Alias restored successfully')
      }
    })
    .catch(error => {
      errorMessage(error.response?.data || 'An error has occurred, please try again later')
      restoreAliasModalOpen.value = false
      restoreAliasLoading.value = false
    })
}

const bulkRestoreAlias = () => {
  bulkRestoreAliasLoading.value = true

  axios
    .post(
      `/api/v1/aliases/restore/bulk`,
      JSON.stringify({
        ids: selectedAliasesToRestore.value.map(a => a.id),
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      // If showing only deleted then reload all aliases
      if (props.currentAliasStatus === 'deleted') {
        router.reload({
          only: [
            'initialRows',
            'search',
            'currentAliasStatus',
            'sort',
            'sortDirection',
            'pinnedFilter',
          ],
          onSuccess: page => {
            bulkRestoreAliasLoading.value = false
            bulkRestoreAliasModalOpen.value = false
            selectedRowIds.value = []
            rows.value = props.initialRows.data
            successMessage(response.data.message)
          },
        })
      } else {
        _.each(selectedAliasesToRestore.value, r => {
          r.deleted_at = null
          r.active = true
        })
        bulkRestoreAliasLoading.value = false
        bulkRestoreAliasModalOpen.value = false
        selectedRowIds.value = []
        successMessage(response.data.message)
      }
    })
    .catch(error => {
      bulkRestoreAliasLoading.value = false
      bulkRestoreAliasModalOpen.value = false
      if (error.response.status === 429) {
        errorMessage('Too many bulk requests, please wait a little while before trying again')
      } else if (error.response.data.message !== undefined) {
        errorMessage(error.response.data.message)
      } else {
        errorMessage()
      }
    })
}

const changeSortDir = () => {
  changeSortDirLoading.value = true

  let params = Object.assign(route().params, {
    sort: props.sortDirection === 'desc' ? _.trimStart(props.sort, '-') : '-' + props.sort,
  })

  router.visit(route(route().current(), _.omit(params, ['page'])), {
    only: ['initialRows', 'search', 'currentAliasStatus', 'sort', 'sortDirection', 'pinnedFilter'],
    onSuccess: page => {
      changeSortDirLoading.value = false
    },
  })
}

const updatePageSize = () => {
  updatePageSizeLoading.value = true

  let params = Object.assign(route().params, {
    page_size: pageSize.value,
  })

  let omit = pageSize.value === 25 ? ['page', 'page_size'] : ['page']

  router.visit(route('aliases.index', _.omit(params, omit)), {
    only: [
      'initialRows',
      'search',
      'sort',
      'sortDirection',
      'currentAliasStatus',
      'initialPageSize',
      'pinnedFilter',
    ],
    onSuccess: page => {
      updatePageSizeLoading.value = false
    },
  })
}

const openDeleteModal = alias => {
  deleteAliasModalOpen.value = true
  aliasToDelete.value = alias
}

const closeDeleteModal = () => {
  deleteAliasModalOpen.value = false
  _.delay(() => (aliasToDelete.value = {}), 300)
}

const openForgetModal = alias => {
  forgetAliasModalOpen.value = true
  aliasToForget.value = alias
}

const closeForgetModal = () => {
  forgetAliasModalOpen.value = false
  _.delay(() => (aliasToForget.value = {}), 300)
}

const openSendFromModal = alias => {
  sendFromAliasDestination.value = ''
  sendFromAliasEmailToSendTo.value = ''
  sendFromAliasCopied.value = false
  sendFromAliasModalOpen.value = true
  aliasToSendFrom.value = alias
}

const closeSendFromModal = () => {
  sendFromAliasModalOpen.value = false
  _.delay(() => (aliasToSendFrom.value = {}), 300)
}

const openRestoreModal = alias => {
  restoreAliasModalOpen.value = true
  aliasToRestore.value = alias
}

const closeRestoreModal = () => {
  restoreAliasModalOpen.value = false
  _.delay(() => (aliasToRestore.value = {}), 300)
}

const openAliasRecipientsModal = alias => {
  editAliasRecipientsModalOpen.value = true
  recipientsAliasToEdit.value = alias
  aliasRecipientsToEdit.value = _.map(alias.recipients, recipient => recipient.id)
}

const closeAliasRecipientsModal = () => {
  editAliasRecipientsModalOpen.value = false
  _.delay(() => (aliasRecipientsToEdit.value = []), 300)
  recipientsAliasToEdit.value = {}
  debounceToolips()
}

const openBulkAliasRecipientsModal = () => {
  bulkEditAliasRecipientsModalOpen.value = true
  aliasRecipientsToEdit.value = []

  // Leave preselected recipients as blank
  /* aliasRecipientsToEdit.value = _
    .chain(selectedRows.value)
    .flatMap(row => row.recipients.map(r => r.id))
    .uniq()
    .take(10)
    .value() */
}

const closeBulkAliasRecipientsModal = () => {
  bulkEditAliasRecipientsModalOpen.value = false
  _.delay(() => (aliasRecipientsToEdit.value = []), 300)
  debounceToolips()
}

const addTooltips = () => {
  if (tippyInstance.value) {
    _.each(tippyInstance.value, instance => instance.destroy())
  }

  tippyInstance.value = tippy('.tooltip', {
    arrow: roundArrow,
    allowHTML: true,
  })
}

const debounceToolips = _.debounce(function () {
  addTooltips()
}, 50)

const recipientsTooltip = recipients => {
  return _.reduce(recipients, (list, recipient) => list + `${recipient.email}<br>`, '')
}

const displaySendFromAddress = alias => {
  errors.value = {}

  if (!validEmail(sendFromAliasDestination.value)) {
    errors.value.sendFromAliasDestination = 'Valid Email required'
    return
  }

  sendFromAliasEmailToSendTo.value = `${alias.local_part}+${sendFromAliasDestination.value.replace(
    '@',
    '=',
  )}@${alias.domain}`
}

const setSendFromAliasCopied = () => {
  sendFromAliasCopied.value = true
}

const getAliasEmail = alias => {
  return alias.extension ? `${alias.local_part}+${alias.extension}@${alias.domain}` : alias.email
}

const isBurnerAlias = alias => !!alias.expires_at || !!alias.max_emails || !!alias.expired_at

const isBurnerExpired = alias => !!alias.expired_at

const burnerBadgeLabel = alias => {
  if (isBurnerExpired(alias)) return 'Expired'
  return 'Burner'
}

const burnerBadgeClasses = alias => {
  if (isBurnerExpired(alias)) {
    return 'bg-grey-100 dark:bg-grey-800 text-grey-500 dark:text-grey-400'
  }
  return 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300'
}

const burnerTooltip = alias => {
  if (isBurnerExpired(alias)) {
    return 'This burner alias has expired.'
  }
  const parts = []
  if (alias.expires_at) {
    const expiry = new Date(alias.expires_at)
    const now = new Date()
    const ms = expiry - now
    if (ms > 0) {
      const hours = Math.floor(ms / 3600000)
      if (hours < 1) {
        parts.push(`Expires in ${Math.max(1, Math.floor(ms / 60000))} min`)
      } else if (hours < 48) {
        parts.push(`Expires in ~${hours} h`)
      } else {
        parts.push(`Expires in ~${Math.floor(hours / 24)} d`)
      }
    }
  }
  if (alias.max_emails) {
    parts.push(`${alias.emails_forwarded} / ${alias.max_emails} emails used`)
  }
  return parts.length ? parts.join(' • ') : 'Burner alias'
}


const getNewAliasEmail = () => {
  return newAliasExtension.value
    ? `${newAliasLocalPart.value}+${newAliasExtension.value}@${newAliasDomain.value}`
    : `${newAliasLocalPart.value}@${newAliasDomain.value}`
}

const getAliasLocalPart = alias => {
  return alias.extension ? `${alias.local_part}+${alias.extension}` : alias.local_part
}

const getAliasStatus = alias => {
  if (alias.deleted_at) {
    return {
      colour: 'red',
      status: 'Deleted',
    }
  } else {
    return {
      colour: alias.active ? 'green' : 'grey',
      status: alias.active ? 'Active' : 'Inactive',
    }
  }
}

const has = (object, path) => {
  return _.has(object, path)
}

const validLocalPart = part => {
  let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))$/
  return re.test(part)
}

const validEmail = email => {
  let re =
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(email)
}

const disabledBulkActivate = () => {
  return !_.find(selectedRows.value, { active: false, deleted_at: null })
}

const disabledBulkDeactivate = () => {
  return !_.find(selectedRows.value, 'active')
}

const disabledBulkPin = () => {
  return !_.find(selectedRows.value, r => !r.pinned)
}

const disabledBulkUnpin = () => {
  return !_.find(selectedRows.value, 'pinned')
}

const disabledBulkDelete = () => {
  return !_.find(selectedRows.value, r => {
    return r.deleted_at === null
  })
}

const disabledBulkRestore = () => {
  return !_.find(selectedRows.value, r => {
    return r.deleted_at !== null
  })
}

const rowStyleClassFn = row => {
  return selectedRowIds.value.includes(row.id) ? 'bg-grey-50 dark:bg-grey-950' : ''
}

const clipboard = (str, success, error) => {
  // Needed as v-clipboard doesn't work inside modals!
  navigator.clipboard.writeText(str).then(
    () => {
      successMessage('Copied to clipboard')
    },
    () => {
      errorMessage('Could not copy to clipboard')
    },
  )
}

const successMessage = (text = '') => {
  notify({
    title: 'Success',
    text: text,
    type: 'success',
  })
}

const errorMessage = (text = 'An error has occurred, please try again later') => {
  notify({
    title: 'Error',
    text: text,
    type: 'error',
  })
}
</script>

<style>
/* Keep the vue-good-table column header in view while scrolling.
   AppLayout's own top bar is h-16 (4rem), so we stick just beneath it.

   vue-good-table wraps its <table> in .vgt-wrap > .vgt-inner-wrap >
   .vgt-responsive, and .vgt-responsive uses overflow:auto for horizontal
   scroll on narrow screens. `position: sticky` can't escape an ancestor
   that's a scrolling context, so we force those wrappers to overflow:visible. */
.aliases-table .vgt-wrap,
.aliases-table .vgt-inner-wrap,
.aliases-table .vgt-responsive,
.aliases-table .vgt-fixed-header {
  overflow: visible !important;
}

.aliases-table .vgt-table thead th {
  position: sticky;
  top: 4rem;
  z-index: 10;
  background: white;
}
/* When the bulk-actions bar is present, push the thead down to stack cleanly. */
.aliases-table:has(#bulk-actions) .vgt-table thead th {
  top: 7rem;
}
.dark .aliases-table .vgt-table thead th {
  background: rgb(17 24 39); /* grey-900 */
}

/* Mobile: hide low-priority columns (Created, Forwards/Blocks, Replies/Sends)
   and let long email text wrap instead of pushing the page wider. */
@media (max-width: 767px) {
  .aliases-table .mobile-hide {
    display: none !important;
  }
  .aliases-table .vgt-table td,
  .aliases-table .vgt-table th {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  .aliases-table .vgt-table {
    word-break: break-word;
  }
}
</style>
