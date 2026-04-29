import './bootstrap'
import '../css/app.css'

import dayjs from 'dayjs'
import advancedFormat from 'dayjs/plugin/advancedFormat'
import relativeTime from 'dayjs/plugin/relativeTime'
import utc from 'dayjs/plugin/utc'

dayjs.extend(advancedFormat)
dayjs.extend(relativeTime)
dayjs.extend(utc)

window.dayjs = dayjs

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'
import Notifications from '@kyvg/vue3-notification'

// Styles
import 'tippy.js/dist/svg-arrow.css'
import 'tippy.js/dist/tippy.css'
import '@vueform/multiselect/themes/default.css'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'mailflusher.com'

import AppLayout from './Layouts/AppLayout.vue'

// Global components
import Icon from './Components/Icon.vue'
import Loader from './Components/Loader.vue'

createInertiaApp({
  progress: {
    color: '#3AE7E1',
    delay: 50,
  },
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ).then(page => {
      page.default.layout = page.default.layout || AppLayout
      return page
    }),
  title: title => `${title} - ${appName}`,
  setup({ el, App, props, plugin }) {
    const mf = createApp({
      render: () => h(App, props),
    })

    mf.use(plugin)
    mf.use(ZiggyVue)
    mf.use(Notifications)

    mf.component('Icon', Icon)
    mf.component('Loader', Loader)

    mf.config.globalProperties.$filters = {
      formatDate(value) {
        return dayjs.utc(value).local().format('Do MMM YYYY')
      },
      formatDateTime(value) {
        return dayjs.utc(value).local().format('Do MMM YYYY h:mm A')
      },
      timeAgo(value) {
        return dayjs.utc(value).fromNow()
      },
      dateTimeNow() {
        return dayjs.utc().format()
      },
      truncate(value, length) {
        if (length >= value.length) {
          return value
        }
        return value.substring(0, length) + '...'
      },
    }

    return mf.mount(el)
  },
})
