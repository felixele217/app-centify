import CustomIntegrationField from '@/types/CustomIntegrationField'
import { CustomIntegrationFieldEnum } from '@/types/Enum/CustomIntegrationFieldEnum'
import customIntegrationField from '@/utils/CustomIntegrationField/customIntegrationField'
import { Ref, ref } from 'vue'

export default function useFieldsRef(
    availableNames: Array<CustomIntegrationFieldEnum>,
    availableFields: Array<CustomIntegrationField>
): Ref<Record<CustomIntegrationFieldEnum, string>> {
    const result: Record<string, string> = {}

    availableNames.forEach((name) => {
        result[name] = customIntegrationField(availableFields, name)?.api_key
    })

    return ref(result)
}
