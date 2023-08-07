import Deal from './Deal'

export default interface Rejection {
    id: number
    created_at: string
    updated_at: string
    deal_id: number
    deal?: Deal
    reason: string
}
