import CustomField from './CustomField'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'

export default interface Integration {
    id: number
    created_at: Date
    updated_at: Date
    name: IntegrationTypeEnum
    access_token: string
    refresh_token: string
    expires_at: Date
    subdomain: Date
    custom_fields: Array<CustomField>
}
