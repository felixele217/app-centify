import Admin from './Admin'
import Agent from './Agent'
import Integration from './Integration'
import Plan from './Plan/Plan'

export default interface Organization {
    id: number
    created_at: Date
    updated_at: Date
    name: string
    admins?: Array<Admin>
    agents?: Array<Agent>
    plans?: Array<Plan>
    integrations?: Array<Integration | null>
}
