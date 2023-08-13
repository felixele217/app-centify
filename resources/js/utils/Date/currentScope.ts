import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import attributionPeriod from './attributionPeriod'
import quarter from './quarter'

export default function currentScope(scope: TimeScopeEnum | '') {
    if (!scope || scope === 'monthly') {
        return attributionPeriod(new Date())
    }

    if (scope === 'quarterly') {
        return `Q${quarter(new Date())}/${new Date().getFullYear()}`
    }

    if (scope === 'annually') {
        return new Date().getFullYear()
    }
}
