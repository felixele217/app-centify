import { CustomIntegrationFieldEnum } from './Enum/CustomIntegrationFieldEnum'
import { IntegrationTypeEnum } from './Enum/IntegrationTypeEnum'

export default interface CustomIntegrationField {
    id: number
    name: CustomIntegrationFieldEnum
    api_key: string
    integration_type: IntegrationTypeEnum
}
