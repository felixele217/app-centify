import Agent from './Agent'
import AgentDealPivot from './AgentDealPivot'
import { DealStatusEnum } from './Enum/DealStatusEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'
import Rejection from './Rejection'

type AgentWithDealPivot = Agent & {
    pivot: AgentDealPivot
}

export default interface Deal {
    id: number
    integration_deal_id: number
    integration_type: IntegrationTypeEnum
    title: string
    status: DealStatusEnum
    value: number
    add_time: string
    won_time: string
    agent_id: number
    s_d_r?: AgentWithDealPivot
    a_e?: AgentWithDealPivot
    agents?: Array<AgentWithDealPivot>
    demo_scheduled_shareholders?: Record<number, AgentWithDealPivot>
    deal_won_shareholders?: Record<number, AgentWithDealPivot>
    note: string | null
    rejections?: Array<Rejection>
    active_rejection?: Rejection
}
