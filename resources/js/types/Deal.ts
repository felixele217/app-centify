import Agent from './Agent'
import { DealStatusEnum } from './Enum/DealStatusEnum'
import { IntegrationEnum } from './Enum/IntegrationEnum'

export default interface Deal {
    id: number
    integration_deal_id: number
    integration_type: IntegrationEnum
    title: string
    status: DealStatusEnum
    value: number
    add_time: Date
    agent_id: number
    agent?: Agent
}
