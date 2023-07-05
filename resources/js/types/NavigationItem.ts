import type { FunctionalComponent } from 'vue'

export default interface NavigationItem {
    name: string
    href: string
    current: boolean
    icon: FunctionalComponent
}
