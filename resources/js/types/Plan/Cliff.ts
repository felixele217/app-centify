import { TimeScopeEnum } from '../Enum/TimeScopeEnum'

export default interface Cliff {
    id: number
    created_at: string
    updated_at: string
    plan_id: number
    threshold_in_percent: number
    time_scope: TimeScopeEnum
}
