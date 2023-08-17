import Admin from './Admin'
import Agent from './Agent'

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: Admin | Agent
    }
    environment: 'production' | 'staging' | 'local'
}
