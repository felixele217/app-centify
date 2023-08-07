import CustomField from './CustomField'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'

export default interface Integration {
    id: number
    created_at: string
    updated_at: string
    name: IntegrationTypeEnum
    access_token: string
    refresh_token: string
    expires_at: string
    subdomain: string
    custom_fields?: Array<CustomField>
}
