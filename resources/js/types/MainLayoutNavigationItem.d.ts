import { FunctionalComponent } from 'vue'

export type MainLayoutNavigationItem = {
    name: string
    href: string
    current: boolean
    icon: FunctionalComponent
}
