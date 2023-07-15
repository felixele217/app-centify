import Admin from './Admin'
import Agent from './Agent'
import { TimeScopeEnum } from './Enum/TimeScopeEnum'

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: Admin | Agent
    }
    time_scopes?: Array<TimeScopeEnum>
}
