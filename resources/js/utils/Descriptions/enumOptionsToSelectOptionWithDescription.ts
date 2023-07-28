import { SelectOptionWithDescription } from '@/Components/Form/SelectWithDescription.vue'

export default function enumOptionsToSelectOptionWithDescription(
    enumValues: Array<string>,
    enumValueToDescription: Record<string, string>
): Array<SelectOptionWithDescription> {
    return enumValues.map((enumValue) => {
        return {
            title: enumValue,
            description: enumValueToDescription[enumValue],
            current: false,
        }
    })
}
