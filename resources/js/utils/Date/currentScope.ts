import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import payoutCycle from './payoutCycle'
import quarter from './quarter'

export default function currentScope(scope: TimeScopeEnum | '') {
    if (!scope || scope === 'monthly') {
        return payoutCycle(new Date())
    }

    if (scope === 'quarterly') {
        return `Q${quarter(new Date())}/${new Date().getFullYear()}`
    }

    if (scope === 'annually') {
        return new Date().getFullYear()
    }
}
