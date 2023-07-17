import CustomIntegrationField from '@/types/CustomIntegrationField'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'

export default function customIntegrationField(
    availableIntegrationFields: Array<CustomIntegrationField>,
    integrationField: CustomIntegrationFieldEnum
) {
    return availableIntegrationFields.filter(
        (customIntegrationField) => customIntegrationField.name === integrationField
    )[0]
}
