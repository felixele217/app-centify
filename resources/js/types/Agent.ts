import AgentDealPivot from './AgentDealPivot'
import AgentPlanPivot from './AgentPlanPivot'
import Deal from './Deal'
import { AgentStatusEnum } from './Enum/AgentStatusEnum'
import Organization from './Organization'
import PaidLeave from './PaidLeave'
import Plan from './Plan/Plan'

export default interface Agent {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: string
    updated_at: string
    base_salary: number | null
    on_target_earning: number | null
    quota_attainment_in_percent?: number
    quota_attainment_change_in_percent?: number | null
    commission?: number
    commission_change?: number | null
    status: AgentStatusEnum
    active_paid_leave?: PaidLeave | null
    sick_leaves_days_count?: number
    vacation_leaves_days_count?: number
    organization: Organization
    organization_id: number
    paid_leaves: Array<PaidLeave>
    deals?: Array<
        Deal & {
            pivot: AgentDealPivot
        }
    >
    plans?: Array<
        Plan & {
            pivot: AgentPlanPivot
        }
    >
    active_plans?: Array<{
        id: number
        name: string
        quota_attainment_in_percent: number
        quota_commission: number
        kicker_commission: number
    }>
}
