import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'

export default function customIntegrationField(
    availableCustomFields: Array<CustomField>,
    customField: CustomFieldEnum
) {
    return availableCustomFields.filter((availableCustomField) => availableCustomField.name === customField)[0]
}
