import { KickerTypeEnum } from '../Enum/KickerTypeEnum'
import { PayoutFrequencyEnum } from '../Enum/PayoutFrequencyEnum'
import { SalaryTypeEnum } from '../Enum/SalaryTypeEnum'
import { TargetVariableEnum } from '../Enum/TargetVariableEnum'
import { TimeScopeEnum } from '../Enum/TimeScopeEnum'

export type UpsertPlanForm = {
    name: string
    start_date: Date | null
    target_amount_per_month: number | null
    target_variable: TargetVariableEnum
    payout_frequency: PayoutFrequencyEnum
    assigned_agent_ids: Array<number>
    cliff: {
        threshold_in_percent: number | null
        time_scope: TimeScopeEnum
    }
    kicker: {
        type: KickerTypeEnum
        threshold_in_percent: number | null
        payout_in_percent: number | null
        salary_type: SalaryTypeEnum
        time_scope: TimeScopeEnum
    }

    cap: number | null
    trigger: 'demo_set_by'
}
