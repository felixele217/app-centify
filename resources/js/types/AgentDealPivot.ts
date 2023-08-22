import { TriggerEnum } from './Enum/TriggerEnum'

type AgentDealPivot = {
    id: number
    agent_id: number
    deal_id: number
    deal_percentage: number
    triggered_by: TriggerEnum
}

export default AgentDealPivot
