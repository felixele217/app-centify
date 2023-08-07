import Agent from './Agent'
import { DealStatusEnum } from './Enum/DealStatusEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'

export default interface Deal {
    id: number
    integration_deal_id: number
    integration_type: IntegrationTypeEnum
    title: string
    status: DealStatusEnum
    value: number
    add_time: string
    agent_id: number
    owner_email: string
    agent?: Agent
    accepted_at: string | null
    declined_at: string | null
}
