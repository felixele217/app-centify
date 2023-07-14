import daysInMonth from '@/utils/daysInMonth'
import { expect, test } from 'vitest'

test('returns correct number of days of the month', () => {
    expect(daysInMonth(new Date('2023-01-15'))).toBe(31)
    expect(daysInMonth(new Date('2023-04-15'))).toBe(30)
    expect(daysInMonth(new Date('2023-05-15'))).toBe(31)
    expect(daysInMonth(new Date('2023-06-15'))).toBe(30)
})
