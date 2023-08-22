import Agent from './Agent'
import Deal from './Deal'
import { TriggerEnum } from './Enum/TriggerEnum'

type AgentDeal = {
    id: number

    agent_id: number
    agents?: Array<Agent>

    deal_id: number
    deals?: Array<Deal>

    deal_percentage: number
    triggered_by: TriggerEnum
}

export default AgentDeal
