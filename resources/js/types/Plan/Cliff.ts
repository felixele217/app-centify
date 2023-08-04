import { TimeScopeEnum } from '../Enum/TimeScopeEnum'

export default interface Cliff {
    id: number
    created_at: Date
    updated_at: Date
    plan_id: number
    threshold_in_percent: number
    time_scope: TimeScopeEnum
}
