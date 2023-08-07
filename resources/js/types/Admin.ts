import Organization from './Organization'

export default interface Admin {
    id: number
    name: string
    email: string
    email_verified_at: string
    created_at: string
    updated_at:string
    organization: Organization
    organization_id: number
}
