export default interface User {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: Date
    updated_at: Date
    active_integrations: {
        pipedrive: boolean
        salesforce: boolean
    }
    base_salary?: number
    on_target_earning?: number
}
