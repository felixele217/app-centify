import paymentCycle from '@/utils/Date/paymentCycle'
import { expect, test } from 'vitest'

test('returns the payment cycle correctly for strings', () => {
    expect(paymentCycle('2022-07-06')).toBe('07/2022')
    expect(paymentCycle('1999-03-25')).toBe('03/1999')
    expect(paymentCycle('2005-11-25')).toBe('11/2005')
})

test('undefined or null as inputs return "-"', () => {
    expect(paymentCycle(undefined)).toBe('-')
    expect(paymentCycle(null)).toBe('-')
})
