import { AgentStatusEnum } from './Enum/AgentStatusEnum'
import Organization from './Organization'
import PaidLeave from './PaidLeave'

export default interface Agent {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: string
    updated_at: string
    base_salary: number | null
    on_target_earning: number | null
    quota_attainment?: number
    quota_attainment_change?: number | null
    commission?: number
    commission_change?: number | null
    status: AgentStatusEnum
    active_paid_leave?: PaidLeave | null
    sick_leaves_days_count?: number
    vacation_leaves_days_count?: number
    organization: Organization
    organization_id: number
    paid_leaves: Array<PaidLeave>
}
