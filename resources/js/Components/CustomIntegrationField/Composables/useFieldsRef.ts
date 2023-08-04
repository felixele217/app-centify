import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'
import customField from '@/utils/CustomField/customField'
import { Ref, ref } from 'vue'

export default function useFieldsRef(
    availableNames: Array<CustomFieldEnum>,
    availableFields: Array<CustomField>
): Ref<Record<CustomIntegrationFieldEnum, string>> {
    const result: Record<string, string> = {}

    availableNames.forEach((name) => {
        result[name] = customField(availableFields, name)?.api_key
    })

    return ref(result)
}
