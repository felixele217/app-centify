import Admin from '../Admin'
import Agent from '../Agent'
import AgentPlanPivot from '../AgentPlanPivot'
import { PlanCycleEnum } from '../Enum/PlanCycleEnum'
import { TargetVariableEnum } from '../Enum/TargetVariableEnum'
import { TriggerEnum } from '../Enum/TriggerEnum'
import Cap from './Cap'
import Cliff from './Cliff'
import Kicker from './Kicker'

export default interface Plan {
    id: number
    organization_id: number
    name: string
    start_date: Date
    target_amount_per_month: number
    trigger: TriggerEnum
    target_variable: TargetVariableEnum
    plan_cycle: PlanCycleEnum
    agents_count?: number
    agents?: Array<
        Agent & {
            pivot: AgentPlanPivot
        }
    >
    creator: Admin
    kicker?: Kicker | null
    cliff?: Cliff | null
    cap?: Cap | null
}
