<template>
  <div class="antialiased min-h-screen bg-grey-50 dark:bg-grey-800">
    <!-- Mobile sidebar overlay -->
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-grey-900/50" />
        </TransitionChild>

        <div class="fixed inset-0 z-50 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel class="relative w-72 flex flex-col bg-white dark:bg-grey-900 shadow-xl">
              <div class="absolute top-0 right-0 -mr-12 pt-4">
                <button
                  type="button"
                  class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                  @click="sidebarOpen = false"
                >
                  <XMarkIcon class="h-6 w-6 text-white" />
                </button>
              </div>

              <!-- Mobile sidebar content -->
              <div class="flex-1 flex flex-col overflow-y-auto">
                <div class="flex items-center gap-3 px-6 h-16 border-b border-grey-100 dark:border-grey-700">
                  <Link :href="route('dashboard.index')" @click="sidebarOpen = false">
                    <img class="h-8 w-auto" src="/svg/icon-logo.svg" alt="Logo" />
                  </Link>
                  <span class="text-lg font-semibold text-grey-900 dark:text-white">MailFlusher</span>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1">
                  <Link
                    v-for="item in sidebarNavigation"
                    :key="item.name"
                    :href="item.locked ? route('subscription.index') : route(item.route)"
                    @click="sidebarOpen = false"
                    :class="[
                      isActive(item.route) && !item.locked
                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                        : item.locked
                          ? 'text-grey-400 hover:bg-grey-50 dark:text-grey-500 dark:hover:bg-grey-800'
                          : 'text-grey-600 hover:bg-grey-50 hover:text-grey-900 dark:text-grey-300 dark:hover:bg-grey-800 dark:hover:text-white',
                      'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors',
                    ]"
                  >
                    <component
                      :is="item.icon"
                      :class="[
                        isActive(item.route) && !item.locked
                          ? 'text-indigo-600 dark:text-indigo-400'
                          : item.locked
                            ? 'text-grey-300 dark:text-grey-600'
                            : 'text-grey-400 group-hover:text-grey-600 dark:text-grey-500 dark:group-hover:text-grey-300',
                        'h-5 w-5 shrink-0',
                      ]"
                    />
                    <span class="flex-1">{{ item.name }}</span>
                    <span
                      v-if="item.locked"
                      class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 dark:text-amber-300"
                      >{{ item.unlockPlan }}</span
                    >
                  </Link>
                </nav>

                <div class="px-3 pb-2 shrink-0 space-y-3">
                  <div
                    v-if="$page.props.user?.usage"
                    class="px-2 pb-1 pt-2 border-t border-grey-100 dark:border-grey-800 space-y-2.5"
                  >
                    <UsageIndicator
                      label="Aliases"
                      :count="$page.props.user.usage.aliases.count"
                      :limit="$page.props.user.usage.aliases.limit"
                    />
                    <UsageIndicator
                      label="Recipients"
                      :count="$page.props.user.usage.recipients.count"
                      :limit="$page.props.user.usage.recipients.limit"
                    />
                  </div>
                  <UpgradeCard
                    v-if="showUpgradeCard"
                    :title="upgradeCopy.title"
                    :subtitle="upgradeCopy.subtitle"
                  />
                </div>

                <div class="px-3 pb-2">
                  <button
                    @click="toggleDarkMode"
                    class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium rounded-lg text-grey-600 hover:bg-grey-50 hover:text-grey-900 dark:text-grey-300 dark:hover:bg-grey-800 dark:hover:text-white transition-colors"
                  >
                    <SunIcon v-if="isDark" class="h-5 w-5 text-grey-400 dark:text-grey-500" />
                    <MoonIcon v-else class="h-5 w-5 text-grey-400" />
                    {{ isDark ? 'Light Mode' : 'Dark Mode' }}
                  </button>
                </div>
                <div class="border-t border-grey-100 dark:border-grey-700 p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-sm font-semibold text-indigo-700 dark:text-indigo-300"
                    >
                      {{ $page.props.user?.username?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-grey-900 dark:text-white truncate">
                        {{ $page.props.user?.username }}
                      </p>
                      <p class="text-xs text-grey-500 dark:text-grey-400 truncate">
                        {{ $page.props.user?.plan_name }} plan
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Desktop sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-30 lg:flex lg:w-64 lg:flex-col">
      <div
        class="flex flex-col flex-grow bg-white dark:bg-grey-900 border-r border-grey-200 dark:border-grey-700"
      >
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 h-16 border-b border-grey-100 dark:border-grey-700 shrink-0">
          <Link :href="route('dashboard.index')">
            <img class="h-8 w-auto" src="/svg/icon-logo.svg" alt="Logo" />
          </Link>
          <span class="text-lg font-semibold text-grey-900 dark:text-white">MailFlusher</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
          <Link
            v-for="item in sidebarNavigation"
            :key="item.name"
            :href="item.locked ? route('subscription.index') : route(item.route)"
            :class="[
              isActive(item.route) && !item.locked
                ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                : item.locked
                  ? 'text-grey-400 hover:bg-grey-50 hover:text-grey-600 dark:text-grey-500 dark:hover:bg-grey-800'
                  : 'text-grey-600 hover:bg-grey-50 hover:text-grey-900 dark:text-grey-300 dark:hover:bg-grey-800 dark:hover:text-white',
              'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors',
            ]"
          >
            <component
              :is="item.icon"
              :class="[
                isActive(item.route) && !item.locked
                  ? 'text-indigo-600 dark:text-indigo-400'
                  : item.locked
                    ? 'text-grey-300 dark:text-grey-600'
                    : 'text-grey-400 group-hover:text-grey-600 dark:text-grey-500 dark:group-hover:text-grey-300',
                'h-5 w-5 shrink-0',
              ]"
            />
            <span class="flex-1">{{ item.name }}</span>
            <span
              v-if="item.locked"
              class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 dark:text-amber-300"
              >{{ item.unlockPlan }}</span
            >
          </Link>
        </nav>

        <!-- Usage & upgrade card -->
        <div class="px-3 pb-2 shrink-0 space-y-3">
          <div
            v-if="$page.props.user?.usage"
            class="px-2 pb-1 pt-2 border-t border-grey-100 dark:border-grey-800 space-y-2.5"
          >
            <UsageIndicator
              label="Aliases"
              :count="$page.props.user.usage.aliases.count"
              :limit="$page.props.user.usage.aliases.limit"
            />
            <UsageIndicator
              label="Recipients"
              :count="$page.props.user.usage.recipients.count"
              :limit="$page.props.user.usage.recipients.limit"
            />
          </div>
          <UpgradeCard
            v-if="showUpgradeCard"
            :title="upgradeCopy.title"
            :subtitle="upgradeCopy.subtitle"
          />
        </div>

        <!-- Dark mode toggle -->
        <div class="px-3 pb-2 shrink-0">
          <button
            @click="toggleDarkMode"
            class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium rounded-lg text-grey-600 hover:bg-grey-50 hover:text-grey-900 dark:text-grey-300 dark:hover:bg-grey-800 dark:hover:text-white transition-colors"
          >
            <SunIcon v-if="isDark" class="h-5 w-5 text-grey-400 dark:text-grey-500" />
            <MoonIcon v-else class="h-5 w-5 text-grey-400" />
            {{ isDark ? 'Light Mode' : 'Dark Mode' }}
          </button>
        </div>

        <!-- User section -->
        <div class="border-t border-grey-100 dark:border-grey-700 p-4 shrink-0">
          <Menu as="div" class="relative">
            <MenuButton
              class="flex items-center gap-3 w-full rounded-lg p-2 hover:bg-grey-50 dark:hover:bg-grey-800 transition-colors"
            >
              <div
                class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-sm font-semibold text-indigo-700 dark:text-indigo-300 shrink-0"
              >
                {{ $page.props.user?.username?.charAt(0)?.toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-sm font-medium text-grey-900 dark:text-white truncate">
                  {{ $page.props.user?.username }}
                </p>
                <p class="text-xs text-grey-500 dark:text-grey-400 truncate">
                  {{ $page.props.user?.plan_name }} plan
                </p>
              </div>
              <ChevronDownIcon class="h-4 w-4 text-grey-400 shrink-0" />
            </MenuButton>

            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute bottom-full left-0 mb-2 w-full rounded-lg shadow-lg py-1 bg-white dark:bg-grey-900 ring-1 ring-grey-200 dark:ring-grey-700 focus:outline-none"
              >
                <MenuItem v-slot="{ active }" as="div">
                  <Link
                    :href="route('settings.show')"
                    :class="[
                      active ? 'bg-grey-50 dark:bg-grey-800' : '',
                      'block px-4 py-2 text-sm text-grey-700 dark:text-grey-200',
                    ]"
                  >
                    Settings
                  </Link>
                </MenuItem>
                <MenuItem v-slot="{ active }" as="div">
                  <a
                    href="https://mailflusher.com/contact"
                    :class="[
                      active ? 'bg-grey-50 dark:bg-grey-800' : '',
                      'block px-4 py-2 text-sm text-grey-700 dark:text-grey-200',
                    ]"
                  >
                    Contact Support
                  </a>
                </MenuItem>
                <div class="border-t border-grey-100 dark:border-grey-700 my-1"></div>
                <MenuItem
                  v-slot="{ active }"
                  as="div"
                  v-if="!$page.props.usesExternalAuthentication"
                >
                  <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    type="button"
                    :class="[
                      active ? 'bg-grey-50 dark:bg-grey-800' : '',
                      'block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400',
                    ]"
                  >
                    Sign out
                  </Link>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>

          <p v-if="$page.props.version" class="text-xs text-grey-400 dark:text-grey-600 text-center mt-3">
            v{{ $page.props.version }} ·
            <a
              href="https://github.com/MailFlusher/mailflusher"
              target="_blank"
              rel="noopener"
              class="hover:underline"
            >Source</a>
          </p>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top header -->
      <header
        class="sticky top-0 z-20 bg-white dark:bg-grey-900 border-b border-grey-200 dark:border-grey-700"
      >
        <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
          <!-- Mobile menu button -->
          <button
            type="button"
            class="lg:hidden -ml-1 mr-3 p-2 rounded-lg text-grey-500 hover:bg-grey-100 dark:hover:bg-grey-800 focus:outline-none"
            @click="sidebarOpen = true"
          >
            <Bars3Icon class="h-6 w-6" />
          </button>

          <!-- Search -->
          <div class="flex-1 flex items-center max-w-2xl">
            <form @submit.prevent="submitSearchForm()" class="w-full">
              <div class="relative">
                <MagnifyingGlassIcon
                  class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-grey-400"
                />
                <input
                  @keyup.esc="
                    search
                      ? $inertia.visit(
                          route(route().current(), omit(route().params, ['search', 'page'])),
                          { only: ['initialRows', 'search'] },
                        )
                      : null
                  "
                  v-model="searchForm.search"
                  class="w-full rounded-lg border border-grey-200 dark:border-grey-700 bg-grey-50 dark:bg-grey-800 py-2 pl-9 pr-4 text-sm text-grey-900 dark:text-white placeholder-grey-400 dark:placeholder-grey-500 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 focus:outline-none"
                  :placeholder="`Search ${searchTypeSelected?.title?.toLowerCase() || ''}...`"
                  type="search"
                />
              </div>
            </form>

            <!-- Search type selector -->
            <Listbox as="div" v-model="searchTypeSelected" class="ml-2 shrink-0">
              <ListboxLabel class="sr-only">Change Search Type</ListboxLabel>
              <div class="relative">
                <ListboxButton
                  class="inline-flex items-center gap-1 rounded-lg border border-grey-200 dark:border-grey-700 bg-white dark:bg-grey-800 px-3 py-2 text-sm font-medium text-grey-700 dark:text-grey-200 hover:bg-grey-50 dark:hover:bg-grey-700 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                >
                  <span class="hidden sm:inline">{{ searchTypeSelected?.title }}</span>
                  <ChevronDownIcon class="h-4 w-4 text-grey-400" />
                </ListboxButton>

                <transition
                  leave-active-class="transition ease-in duration-100"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <ListboxOptions
                    class="absolute z-10 right-0 mt-2 w-64 rounded-lg shadow-lg bg-white dark:bg-grey-900 ring-1 ring-grey-200 dark:ring-grey-700 py-1 focus:outline-none"
                  >
                    <ListboxOption
                      as="template"
                      v-for="option in searchOptions"
                      :key="option.title"
                      :value="option"
                      v-slot="{ active, selected }"
                    >
                      <li
                        :class="[
                          active ? 'bg-grey-50 dark:bg-grey-800' : '',
                          'cursor-pointer select-none px-4 py-2.5',
                        ]"
                      >
                        <div class="flex items-center justify-between">
                          <div>
                            <p
                              :class="[
                                selected ? 'font-semibold' : 'font-normal',
                                'text-sm text-grey-900 dark:text-white',
                              ]"
                            >
                              {{ option.title }}
                            </p>
                            <p class="text-xs text-grey-500 dark:text-grey-400 mt-0.5">
                              {{ option.description }}
                            </p>
                          </div>
                          <CheckIcon
                            v-if="selected"
                            class="h-4 w-4 text-indigo-600 dark:text-indigo-400 shrink-0 ml-2"
                          />
                        </div>
                      </li>
                    </ListboxOption>
                  </ListboxOptions>
                </transition>
              </div>
            </Listbox>
          </div>

          <!-- Right side -->
          <div class="ml-4 flex items-center gap-3">
            <span class="hidden md:block text-sm font-medium text-grey-700 dark:text-grey-200">
              {{ $page.props.user?.username }}
            </span>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-screen-xl mx-auto">
          <slot />
        </div>
      </main>
    </div>

    <notifications position="bottom right" />

    <FlashNotification v-if="$page.props.flash">
      <template v-slot:icon>
        <CheckCircleIcon class="h-6 w-6 text-white" aria-hidden="true" />
      </template>
      <template v-slot:message>
        {{ $page.props.flash }}
      </template>
    </FlashNotification>
  </div>
</template>

<script setup>
import { router, useForm, usePage, Link } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import {
  Dialog,
  DialogPanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot,
  Listbox,
  ListboxButton,
  ListboxLabel,
  ListboxOption,
  ListboxOptions,
} from '@headlessui/vue'
import {
  Cog6ToothIcon,
  WrenchScrewdriverIcon,
  UsersIcon,
  HomeIcon,
  Bars3Icon,
  InboxArrowDownIcon,
  XMarkIcon,
  GlobeAltIcon,
  AtSymbolIcon,
  ExclamationTriangleIcon,
  FunnelIcon,
  CheckCircleIcon,
  NoSymbolIcon,
  FolderIcon,
  LockClosedIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'
import FlashNotification from './../Components/FlashNotification.vue'
import UsageIndicator from './../Components/UsageIndicator.vue'
import UpgradeCard from './../Components/UpgradeCard.vue'

const props = defineProps({
  search: {
    type: String,
  },
})

const page = usePage()

const isDark = ref(
  page.props.user?.darkMode ?? window.matchMedia('(prefers-color-scheme: dark)').matches,
)

const toggleDarkMode = () => {
  isDark.value = !isDark.value

  // Toggle class immediately for instant feedback
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    document.body.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
    document.body.classList.remove('dark')
  }

  // Persist to server
  axios.post(route('settings.dark_mode'), { dark_mode: isDark.value })
}

const sidebarNavigation = computed(() => {
  const u = page.props.user
  const items = [
    { name: 'Dashboard', route: 'dashboard.index', icon: HomeIcon },
    { name: 'Aliases', route: 'aliases.index', icon: AtSymbolIcon },
    { name: 'Groups', route: 'alias_groups.index', icon: FolderIcon },
    { name: 'Recipients', route: 'recipients.index', icon: InboxArrowDownIcon },
    { name: 'Usernames', route: 'usernames.index', icon: UsersIcon },
    {
      name: 'Domains',
      route: 'domains.index',
      icon: GlobeAltIcon,
      locked: !u?.can_use_custom_domains,
      unlockPlan: 'Pro',
    },
    {
      name: 'Rules',
      route: 'rules.index',
      icon: FunnelIcon,
      locked: !u?.can_use_rules,
      unlockPlan: 'Standard',
    },
    {
      name: 'Failed Deliveries',
      route: 'failed_deliveries.index',
      icon: ExclamationTriangleIcon,
      locked: !u?.can_view_failed_deliveries,
      unlockPlan: 'Standard',
    },
    {
      name: 'Blocklist',
      route: 'blocklist.index',
      icon: NoSymbolIcon,
      locked: !u?.can_use_blocklist,
      unlockPlan: 'Standard',
    },
    {
      name: 'Ghost Inbox',
      route: 'ghost_inbox.index',
      icon: LockClosedIcon,
      locked: !u?.can_use_ghost_inbox,
      unlockPlan: 'Pro',
    },
    { name: 'Settings', route: 'settings.show', icon: Cog6ToothIcon },
  ]

  if (u?.is_admin) {
    items.push({ name: 'Control Panel', route: 'admin.index', icon: WrenchScrewdriverIcon })
  }

  // "Domains" is additionally gated by the global enableCustomDomains toggle —
  // hide entirely when globally disabled, otherwise show (possibly locked).
  return items.filter(item => {
    if (item.name === 'Domains' && !page.props.enableCustomDomains && !u?.can_use_custom_domains)
      return false
    return true
  })
})

const showUpgradeCard = computed(() => {
  const u = page.props.user
  if (!u || u.plan === 'pro') return false
  const usage = u.usage || {}
  const near = key => {
    const slot = usage[key]
    if (!slot || slot.limit === null || slot.limit === undefined) return false
    return slot.limit > 0 && slot.count / slot.limit >= 0.6
  }
  // Always show for free users; for standard, only show when nearing a limit
  if (u.plan === 'free') return true
  return near('aliases') || near('recipients') || near('rules')
})

const upgradeCopy = computed(() => {
  const u = page.props.user
  if (u?.plan === 'free') {
    return { title: 'Upgrade to Standard', subtitle: '€1/month — 20 aliases, reply, rules' }
  }
  return { title: 'Upgrade to Pro', subtitle: '€5/month — unlimited aliases, custom domains' }
})

const sidebarOpen = ref(false)

const isActive = routeName => {
  const current = route().current()
  const prefix = routeName.split('.')[0]
  return current?.startsWith(prefix)
}

const searchForm = useForm({
  search: props.search ?? '',
})

const searchOptions = computed(() => {
  const items = [
    { title: 'Aliases', route: 'aliases.index', description: 'Search by email or description' },
    { title: 'Recipients', route: 'recipients.index', description: 'Search by email' },
    {
      title: 'Usernames',
      route: 'usernames.index',
      description: 'Search by username or description',
    },
    { title: 'Domains', route: 'domains.index', description: 'Search by domain or description' },
    { title: 'Rules', route: 'rules.index', description: 'Search by name' },
    {
      title: 'Failed Deliveries',
      route: 'failed_deliveries.index',
      description: 'Search by error message',
    },
    {
      title: 'Blocklist',
      route: 'blocklist.index',
      description: 'Search by value or type',
    },
  ]

  return items.filter(item => {
    if (item.title === 'Domains' && !page.props.enableCustomDomains) return false
    if (item.title === 'Rules' && !page.props.user?.can_use_rules) return false
    if (item.title === 'Failed Deliveries' && !page.props.user?.can_view_failed_deliveries)
      return false
    if (item.title === 'Blocklist' && !page.props.user?.can_use_blocklist) return false
    return true
  })
})

const searchTypeSelected = ref(
  _.find(searchOptions.value, ['title', _.startCase(usePage().component.split('/')[0])]) ??
    searchOptions.value[0],
)

watch(
  () => usePage().component,
  function (component) {
    searchTypeSelected.value =
      _.find(searchOptions.value, ['title', _.startCase(component.split('/')[0])]) ??
      searchOptions.value[0]

    if (!props.search) {
      searchForm.search = ''
    }
  },
)

watch(
  () => props.search,
  function (search) {
    if (!search) {
      searchForm.search = ''
    }
  },
)

const submitSearchForm = () => {
  if (!searchForm.search.length && props.search) {
    router.visit(route(route().current(), _.omit(route().params, ['search', 'page', 'id'])), {
      only: ['initialRows', 'search'],
    })
  } else if (searchForm.search.length > 1) {
    searchForm.get(
      route(searchTypeSelected.value.route, _.omit(route().params, ['search', 'page', 'id'])),
      {
        only: ['initialRows', 'search'],
      },
    )
  }
}

const omit = (object, key) => {
  return _.omit(object, key)
}
</script>
