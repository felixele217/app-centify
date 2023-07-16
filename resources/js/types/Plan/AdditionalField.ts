import { AdditionalFieldTypes } from './AdditionalFieldTypes'

export type AdditionalField = {
    id: number
    type: AdditionalFieldTypes
    value?: number
} & {
    type: 'Cap'
    value: number
}
