import { SelectOption } from '@/Components/Form/SelectWithDescription.vue'
import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'
import { payoutFrequencyDescription } from './payoutFrequencyDescription'

export default function titleToDescription(titles: Array<string>, forEnum: 'PayoutFrequencyEnum'): Array<SelectOption> {
    switch (forEnum) {
        case 'PayoutFrequencyEnum':
            return titles.map((title) => {
                return {
                    title: title,
                    description: payoutFrequencyDescription[title as PayoutFrequencyEnum],
                    current: false,
                }
            })
    }
}
