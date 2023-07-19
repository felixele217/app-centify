import positionOfDayInYear from '@/utils/Date/positionOfDayInYear'
import { expect, test } from 'vitest'

test('correctly returns position in year', () => {
    expect(positionOfDayInYear(new Date('2022-01-01'))).toBe(1)
    expect(positionOfDayInYear(new Date('2022-12-31'))).toBe(365)
    expect(positionOfDayInYear(new Date('2022-04-26'))).toBe(116)
})
