import Admin from '../Admin'
import Agent from '../Agent'
import AgentPlanPivot from '../AgentPlanPivot'
import { PlanCycleEnum } from '../Enum/PlanCycleEnum'
import { TargetVariableEnum } from '../Enum/TargetVariableEnum'
import Cap from './Cap'
import Cliff from './Cliff'
import Kicker from './Kicker'

export default interface Plan {
    id: number
    organization_id: number
    name: string
    start_date: Date
    target_amount_per_month: number
    target_variable: TargetVariableEnum
    plan_cycle: PlanCycleEnum
    agents_count?: number
    agents?: Array<
        Agent & {
            pivot: AgentPlanPivot
        }
    >
    creator: Admin
    cliff?: Cliff
    kicker?: Kicker
    cap?: Cap
}
