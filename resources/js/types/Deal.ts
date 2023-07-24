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
    add_time: Date
    agent_id: number
    owner_email: string
    agent?: Agent
    accepted_at: Date | null
    declined_at: Date | null
}
