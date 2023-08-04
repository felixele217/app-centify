import CustomField from '@/types/CustomField'
import { CustomFieldEnum } from '@/types/Enum/CustomFieldEnum'
import customField from '@/utils/CustomField/customField'
import { Ref, ref } from 'vue'

export default function useCustomFieldRefs(
    availableNames: Array<CustomFieldEnum>,
    availableFields: Array<CustomField>
): Ref<Record<CustomFieldEnum, string>> {
    const result: Record<string, string> = {}

    availableNames.forEach((name) => {
        result[name] = customField(availableFields, name)?.api_key
    })

    return ref(result)
}
