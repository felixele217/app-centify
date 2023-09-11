import Admin from './Admin'
import Agent from './Agent'
import Integration from './Integration'
import Plan from './Plan/Plan'

export default interface Organization {
    id: number
    created_at: string
    updated_at: string
    name: string
    admins?: Array<Admin>
    agents?: Array<Agent>
    plans?: Array<Plan>
    integrations?: Array<Integration | null>
    auto_accept_demo_scheduled: boolean
    auto_accept_deal_won: boolean
}
