import { SelectOptionWithDescription } from '@/Components/Form/SelectWithDescription.vue'

export default function enumOptionsToSelectOptionWithDescription(
    titles: Array<string>,
    titleToDescription: Record<string, string>
): Array<SelectOptionWithDescription> {
    return titles.map((title) => {
        return {
            title: title,
            description: titleToDescription[title],
            current: false,
        }
    })
}
