import attributionPeriod from '@/utils/Date/attributionPeriod'
import { expect, test } from 'vitest'

test('returns the payment cycle correctly for strings', () => {
    expect(attributionPeriod('2022-07-06')).toBe('07/2022')
    expect(attributionPeriod('1999-03-25')).toBe('03/1999')
    expect(attributionPeriod('2005-11-25')).toBe('11/2005')
})

test('returns the payment cycle correctly for Dates', () => {
    expect(attributionPeriod(new Date('2022-07-06'))).toBe('07/2022')
    expect(attributionPeriod(new Date('1999-03-25'))).toBe('03/1999')
    expect(attributionPeriod(new Date('2005-11-25'))).toBe('11/2005')
})

test('undefined or null as inputs return "-"', () => {
    expect(attributionPeriod(undefined)).toBe('-')
    expect(attributionPeriod(null)).toBe('-')
})
