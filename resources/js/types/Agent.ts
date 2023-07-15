import { AgentStatusEnum } from './Enum/AgentStatusEnum'

export default interface Agent {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: Date
    updated_at: Date
    base_salary: number | null
    on_target_earning: number | null
    quota_attainment?: number
    commission?: number
    status: AgentStatusEnum
}
