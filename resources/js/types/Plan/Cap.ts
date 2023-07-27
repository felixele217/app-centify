import { SalaryTypeEnum } from "../Enum/SalaryTypeEnum"

export default interface Cap {
    id: number
    created_at: Date
    updated_at: Date
    value: SalaryTypeEnum
}
