import { AgentStatusEnum } from './Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from './Enum/ContinuationOfPayTimeScopeEnum'

export default interface PaidLeave {
    id: number
    created_at: Date
    updated_at: Date
    start_date: Date
    end_date: Date
    continuation_of_pay_time_scope: ContinuationOfPayTimeScopeEnum
    sum_of_commissions: number
    reason: AgentStatusEnum
}
