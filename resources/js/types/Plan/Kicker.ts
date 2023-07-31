import { KickerTypeEnum } from '../Enum/KickerTypeEnum'
import { SalaryTypeEnum } from '../Enum/SalaryTypeEnum'
import { TimeScopeEnum } from '../Enum/TimeScopeEnum'

export default interface Kicker {
    id: number
    created_at: Date
    updated_at: Date
    type: KickerTypeEnum
    threshold_in_percent: number
    payout_in_percent: number
    salary_type: SalaryTypeEnum
    time_scope: TimeScopeEnum
}
