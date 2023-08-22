import { TriggerEnum } from './Enum/TriggerEnum'

type AgentDealPivot = {
    agent_id: number
    deal_id: number
    deal_percentage: number
    triggered_by: TriggerEnum
}

export default AgentDealPivot
