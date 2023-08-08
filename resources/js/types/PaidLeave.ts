import { AgentStatusEnum } from './Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from './Enum/ContinuationOfPayTimeScopeEnum'

export default interface PaidLeave {
    id: number
    created_at: string
    updated_at: string
    start_date: string
    end_date: string | null
    continuation_of_pay_time_scope: ContinuationOfPayTimeScopeEnum
    sum_of_commissions: number
    reason: AgentStatusEnum
}
