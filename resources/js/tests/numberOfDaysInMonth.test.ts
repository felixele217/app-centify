import numberOfDaysInMonth from '@/utils/Date/numberOfDaysInMonth'
import { expect, test } from 'vitest'

test('returns correct number of days of the month', () => {
    expect(numberOfDaysInMonth(new Date('2023-01-15'))).toBe(31)
    expect(numberOfDaysInMonth(new Date('2023-04-15'))).toBe(30)
    expect(numberOfDaysInMonth(new Date('2023-05-15'))).toBe(31)
    expect(numberOfDaysInMonth(new Date('2023-06-15'))).toBe(30)
})
