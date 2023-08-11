import payoutCycle from '@/utils/Date/payoutCycle'
import { expect, test } from 'vitest'

test('returns the payment cycle correctly for strings', () => {
    expect(payoutCycle('2022-07-06')).toBe('07/2022')
    expect(payoutCycle('1999-03-25')).toBe('03/1999')
    expect(payoutCycle('2005-11-25')).toBe('11/2005')
})

test('returns the payment cycle correctly for Dates', () => {
    expect(payoutCycle(new Date('2022-07-06'))).toBe('07/2022')
    expect(payoutCycle(new Date('1999-03-25'))).toBe('03/1999')
    expect(payoutCycle(new Date('2005-11-25'))).toBe('11/2005')
})

test('undefined or null as inputs return "-"', () => {
    expect(payoutCycle(undefined)).toBe('-')
    expect(payoutCycle(null)).toBe('-')
})
