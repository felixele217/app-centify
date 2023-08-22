import Agent from './Agent'
import AgentDealPivot from './AgentDealPivot'
import { DealStatusEnum } from './Enum/DealStatusEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'
import Rejection from './Rejection'

export default interface Deal {
    id: number
    integration_deal_id: number
    integration_type: IntegrationTypeEnum
    title: string
    status: DealStatusEnum
    value: number
    add_time: string
    agent_id: number
    s_d_r?: Agent & {
        pivot: AgentDealPivot
    }
    a_e?: Agent & {
        pivot: AgentDealPivot
    }
    agents?: Array<
        Agent & {
            pivot: AgentDealPivot
        }
    >
    note: string | null
    rejections?: Array<Rejection>
    active_rejection?: Rejection
}
