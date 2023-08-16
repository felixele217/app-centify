import { CustomFieldEnumCases } from '@/EnumCases/CustomFieldEnum'
import Integration from '@/types/Integration'

export default function hasMissingCustomField(activeIntegration: Integration | null) {
    if (!activeIntegration) {
        return false
    }

    return activeIntegration!.custom_fields?.length !== CustomFieldEnumCases.length
}
