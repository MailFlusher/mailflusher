<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <div class="pt-10">
        <div class="space-y-1">
          <h3 class="text-xl font-medium leading-6 text-grey-900 dark:text-white">
            Subscription
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            Manage your subscription plan.
          </p>
        </div>

        <!-- Current plan -->
        <div class="mt-6 bg-grey-50 dark:bg-grey-800 rounded-xl p-6 border border-grey-200 dark:border-grey-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-grey-500 dark:text-grey-400">Current plan</p>
              <p class="text-2xl font-bold text-grey-900 dark:text-white mt-1">
                {{ plans[currentPlan]?.name || 'Free' }}
              </p>
              <p class="text-sm text-grey-500 dark:text-grey-400 mt-1">
                {{ currentPlan === 'free' ? 'No active subscription' : `€${plans[currentPlan]?.price}/month` }}
              </p>
            </div>
            <span
              class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
              :class="{
                'bg-grey-200 text-grey-600 dark:bg-grey-700 dark:text-grey-300': currentPlan === 'free',
                'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300': currentPlan === 'standard',
                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': currentPlan === 'pro',
              }"
            >{{ plans[currentPlan]?.name || 'Free' }}</span>
          </div>

          <div v-if="onGracePeriod" class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
              Your subscription has been cancelled and will end on <strong>{{ $filters.formatDate(subscriptionEndsAt) }}</strong>.
              You can resume it before then to keep your current plan.
            </p>
            <form @submit.prevent="resumeSubscription" class="mt-3">
              <button
                type="submit"
                :disabled="resumeLoading"
                class="inline-flex items-center rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 text-sm font-medium disabled:opacity-50"
              >
                Resume Subscription
                <loader v-if="resumeLoading" />
              </button>
            </form>
          </div>
        </div>

        <!-- Plan options -->
        <div class="mt-8">
          <h4 class="text-lg font-medium text-grey-900 dark:text-white mb-4">
            {{ currentPlan === 'free' ? 'Upgrade your plan' : 'Change plan' }}
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              v-for="(plan, key) in plans"
              :key="key"
              class="rounded-xl border p-5 flex flex-col"
              :class="key === currentPlan ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-600' : 'border-grey-200 dark:border-grey-700'"
            >
              <div class="flex items-center justify-between mb-3">
                <h5 class="font-semibold text-grey-900 dark:text-white">{{ plan.name }}</h5>
                <span v-if="key === currentPlan" class="text-xs font-medium text-indigo-600 dark:text-indigo-400">Current</span>
              </div>

              <p class="text-2xl font-bold text-grey-900 dark:text-white">
                {{ plan.price === 0 ? 'Free' : `€${plan.price}` }}
                <span v-if="plan.price > 0" class="text-sm font-normal text-grey-500">/month</span>
              </p>

              <ul class="mt-4 space-y-2 text-sm text-grey-600 dark:text-grey-300 flex-grow">
                <li>{{ plan.aliases === null ? 'Unlimited' : plan.aliases }} aliases</li>
                <li>{{ plan.recipients }} {{ plan.recipients === 1 ? 'recipient' : 'recipients' }}</li>
                <li>{{ plan.rules === 0 ? 'No rules' : plan.rules + ' rules' }}</li>
                <li>{{ plan.can_reply_send ? 'Reply/send from aliases' : 'No reply/send' }}</li>
                <li>{{ plan.bandwidth === null ? 'Unlimited bandwidth' : Math.round(plan.bandwidth / 1024 / 1024) + ' MB bandwidth' }}</li>
              </ul>

              <div class="mt-4">
                <button
                  v-if="key !== currentPlan && key !== 'free'"
                  @click="subscribe(key)"
                  :disabled="checkoutLoading === key"
                  class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 text-sm font-medium disabled:opacity-50"
                >
                  <span v-if="checkoutLoading === key">Redirecting...</span>
                  <span v-else>{{ currentPlan === 'free' ? 'Upgrade' : 'Switch' }} to {{ plan.name }}</span>
                </button>
                <button
                  v-else-if="key === currentPlan && key !== 'free' && !onGracePeriod"
                  @click="cancelSubscription"
                  :disabled="cancelLoading"
                  class="w-full rounded-lg border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2 text-sm font-medium disabled:opacity-50"
                >
                  Cancel subscription
                  <loader v-if="cancelLoading" />
                </button>
                <div v-else-if="key === 'free'" class="text-center text-sm text-grey-400 py-2">
                  {{ currentPlan === 'free' ? 'Your current plan' : 'Cancel to downgrade' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Promo code redemption -->
        <div class="mt-8">
          <h4 class="text-lg font-medium text-grey-900 dark:text-white mb-2">
            Have a promo code?
          </h4>
          <p class="text-sm text-grey-500 dark:text-grey-400 mb-3">
            Redeem a code to add time to your plan. Stacks on top of any time you already have.
          </p>
          <form @submit.prevent="redeemPromo" class="flex items-start gap-3 max-w-md">
            <div class="flex-grow">
              <input
                v-model="promoForm.code"
                type="text"
                autocomplete="off"
                spellcheck="false"
                placeholder="Enter code"
                class="w-full rounded-lg border-grey-300 dark:border-grey-700 dark:bg-grey-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase"
                :disabled="promoForm.processing"
              />
              <p v-if="promoForm.errors.code" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ promoForm.errors.code }}
              </p>
            </div>
            <button
              type="submit"
              :disabled="promoForm.processing || !promoForm.code"
              class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 text-sm font-medium disabled:opacity-50"
            >
              <span v-if="promoForm.processing">Redeeming...</span>
              <span v-else>Redeem</span>
            </button>
          </form>
        </div>

        <!-- Billing portal -->
        <div v-if="hasSubscription" class="mt-8">
          <a
            :href="route('subscription.portal')"
            class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500"
          >
            Manage billing details &amp; invoices &rarr;
          </a>
        </div>
      </div>
    </div>
  </SettingsLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import SettingsLayout from '../../Layouts/SettingsLayout.vue'

const props = defineProps({
  currentPlan: { type: String, required: true },
  plans: { type: Object, required: true },
  hasSubscription: { type: Boolean, default: false },
  onGracePeriod: { type: Boolean, default: false },
  subscriptionEndsAt: { type: String, default: null },
})

const checkoutLoading = ref(null)
const cancelLoading = ref(false)
const resumeLoading = ref(false)

const subscribe = plan => {
  checkoutLoading.value = plan

  axios
    .post(route('subscription.checkout'), { plan })
    .then(response => {
      // Cashier returns a redirect URL for Stripe Checkout
      window.location.href = response.data.url || response.request.responseURL
    })
    .catch(error => {
      checkoutLoading.value = null
      alert(error.response?.data?.message || error.response?.data || 'An error occurred')
    })
}

const cancelSubscription = () => {
  if (!confirm('Are you sure you want to cancel your subscription? It will remain active until the end of the billing period.')) return

  cancelLoading.value = true
  router.post(route('subscription.cancel'), {}, {
    onFinish: () => { cancelLoading.value = false },
  })
}

const resumeSubscription = () => {
  resumeLoading.value = true
  router.post(route('subscription.resume'), {}, {
    onFinish: () => { resumeLoading.value = false },
  })
}

const promoForm = useForm({ code: '' })

const redeemPromo = () => {
  promoForm.transform(data => ({ code: data.code.trim().toUpperCase() })).post(route('promo.redeem'), {
    preserveScroll: true,
    onSuccess: () => promoForm.reset('code'),
  })
}
</script>
