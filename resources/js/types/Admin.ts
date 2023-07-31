import Organization from "./Organization"

export default interface Admin {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: Date
    updated_at: Date
    organization: Organization
    organization_id: number
}
