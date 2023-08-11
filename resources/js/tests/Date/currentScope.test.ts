import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import payoutCycle from '@/utils/Date/payoutCycle'
import quarter from '@/utils/Date/quarter'
import { expect, it, test } from 'vitest'

it('returns correct format for month scope', () => {
    const scope: TimeScopeEnum = 'monthly'

    expect(currentScope(scope)).toBe(payoutCycle(new Date()))
})

it('returns correct format for quarter scope', () => {
    const scope: TimeScopeEnum = 'quarterly'

    expect(currentScope(scope)).toBe(`Q${quarter(new Date())}/${new Date().getFullYear()}`)
})

it('returns correct format for year scope', () => {
    const scope: TimeScopeEnum = 'annually'

    expect(currentScope(scope)).toBe(new Date().getFullYear())
})

test('empty string as input returns the monthly scope', () => {
    expect(currentScope('')).toBe(payoutCycle(new Date()))
})
