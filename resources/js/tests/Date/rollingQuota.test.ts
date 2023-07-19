import numberOfDaysInMonth from '@/utils/Date/numberOfDaysInMonth'
import numberOfDaysInQuarter from '@/utils/Date/numberOfDaysInQuarter'
import dayPositionInQuarter from '@/utils/Date/positionOfDayInQuarter'
import positionOfDayInYear from '@/utils/Date/positionOfDayInYear'
import rollingQuota from '@/utils/Date/rollingQuota'
import roundFloat from '@/utils/roundFloat'
import { expect, test } from 'vitest'

test('the rolling quarter returns correctly for the current month', () => {
    expect(rollingQuota('monthly')).toBe(roundFloat(new Date().getDate() / numberOfDaysInMonth()))
})

test('the rolling quarter returns correctly for the current quarter', () => {
    expect(rollingQuota('quarterly')).toBe(
        roundFloat(dayPositionInQuarter(new Date()) / numberOfDaysInQuarter(new Date()))
    )
})

test('the rolling quarter returns correctly for the current year', () => {
    expect(rollingQuota('annually')).toBe(roundFloat(positionOfDayInYear(new Date()) / 365))
})
