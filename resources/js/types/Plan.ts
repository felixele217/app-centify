import Admin from './Admin'
import Agent from './Agent'
import Cliff from './Cliff'
import type { PayoutFrequencyEnum } from './Enum/PayoutFrequencyEnum'
import type { TargetVariableEnum } from './Enum/TargetVariableEnum'

export default interface Plan {
    id: number
    organization_id: number
    name: string
    start_date: Date
    target_amount_per_month: number
    target_variable: TargetVariableEnum
    payout_frequency: PayoutFrequencyEnum
    agents_count?: number
    agents?: Array<Agent>
    creator: Admin
    cliff?: Cliff
}
