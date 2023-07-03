import type { PayoutFrequencyEnum } from './Enum/PayoutFrequencyEnum'
import type { TargetVariableEnum } from './Enum/TargetVariableEnum'
import User from './User'

export default interface Plan {
    id: number
    organization_id: number
    name: string
    start_date: Date
    target_amount_per_month: number
    target_variable: TargetVariableEnum
    payout_frequency: PayoutFrequencyEnum
    agents_count?: number
    creator: User
}
