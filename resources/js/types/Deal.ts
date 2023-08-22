import Agent from './Agent'
import AgentDealPivot from './AgentDeal'
import { DealStatusEnum } from './Enum/DealStatusEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'
import Rejection from './Rejection'
import Split from './Split'

export default interface Deal {
    id: number
    integration_deal_id: number
    integration_type: IntegrationTypeEnum
    title: string
    status: DealStatusEnum
    value: number
    add_time: string
    agent_id: number
    agents?: Array<Agent & {
        pivot: AgentDealPivot
    }>
    accepted_at: string | null
    note: string | null
    rejections?: Array<Rejection>
    active_rejection?: Rejection
}
