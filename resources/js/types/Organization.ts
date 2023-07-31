import Admin from './Admin'
import Agent from './Agent'
import CustomIntegrationField from './CustomIntegrationField'
import Plan from './Plan/Plan'

export default interface Organization {
    id: number
    created_at: Date
    updated_at: Date
    name: string
    admins?: Array<Admin>
    agents?: Array<Agent>
    plans?: Array<Plan>
    active_integrations: {
        pipedrive: null | string
        salesforce: null | string
    }
    custom_integration_fields: Array<CustomIntegrationField>
    pipedrive_config?: {
        subdomain: string
    }
}
