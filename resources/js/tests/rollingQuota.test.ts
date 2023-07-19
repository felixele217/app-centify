import numberOfDaysInMonth from '@/utils/numberOfDaysInMonth'
import numberOfDaysInQuarter from '@/utils/numberOfDaysInQuarter'
import roundFloat from '@/utils/roundFloat'
import { expect, test } from 'vitest'

test('the rolling quarter returns correctly for the current month', () => {
    expect(rollingQuota('monthly')).toBe(roundFloat(new Date().getDate() / numberOfDaysInMonth()))
})

test('the rolling quarter returns correctly for the current quarter', () => {
    expect(rollingQuota('quarterly')).toBe(roundFloat(
         numberOfDaysInQuarter(new Date())
    ))
})

test('the rolling quarter returns correctly for the current year', () => {
    expect(rollingQuota('annually')).toBe(roundFloat(

    ))
})
