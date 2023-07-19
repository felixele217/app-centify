import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import roundFloat from '../roundFloat'
import numberOfDaysInMonth from './numberOfDaysInMonth'
import numberOfDaysInQuarter from './numberOfDaysInQuarter'
import dayPositionInQuarter from './positionOfDayInQuarter'
import positionOfDayInYear from './positionOfDayInYear'

export default function rollingQuota(timeScope: TimeScopeEnum): number {
    if (timeScope === 'quarterly') {
        return roundFloat(dayPositionInQuarter(new Date()) / numberOfDaysInQuarter(new Date()))
    }

    if (timeScope === 'annually') {
        return roundFloat(positionOfDayInYear(new Date()) / 365)
    }

    return roundFloat(new Date().getDate() / numberOfDaysInMonth())
}
