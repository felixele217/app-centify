import { CustomFieldEnum } from './Enum/CustomFieldEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'

export default interface CustomField {
    id: number
    name: CustomFieldEnum
    api_key: string
    integration_id: number
    integration_type: IntegrationTypeEnum
}
