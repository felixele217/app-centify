import dayPositionInQuarter from '@/utils/Date/dayPositionInQuarter'
import { expect, test } from 'vitest'

test('correctly returns number', () => {
    expect(dayPositionInQuarter(new Date('2023-08-05'))).toBe(36)
    expect(dayPositionInQuarter(new Date('2023-02-03'))).toBe(34)
    expect(dayPositionInQuarter(new Date('2023-12-03'))).toBe(64)
})
