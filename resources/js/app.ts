import '../css/app.css'
import './bootstrap'

import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createApp, h } from 'vue'
// @ts-ignore
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m.js'
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Centify'
// createInertiaApp({
//     resolve: (name) => {
//         const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
//         let page = pages[`./Pages/${name}.vue`]
//         page.default.layout = page.default.layout || Layout
//         return page
//     },
//     // ...
// })

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page: any = pages[`./Pages/${name}.vue`]
        page.default.layout = page.default.layout || MainLayout
        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
