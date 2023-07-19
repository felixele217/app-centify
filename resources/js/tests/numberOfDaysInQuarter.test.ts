import numberOfDaysInQuarter from '@/utils/numberOfDaysInQuarter'
import { describe, expect, test } from 'vitest'

describe('correctly returns number of days for each quarter', () => {
    test('1st quarter', () => {
        expect(numberOfDaysInQuarter(new Date('2022-01-15'))).toBe(90)
    })

    test('2nd quarter', () => {
        expect(numberOfDaysInQuarter(new Date('2022-04-15'))).toBe(91)
    })

    test('3rd quarter', () => {
        expect(numberOfDaysInQuarter(new Date('2022-07-15'))).toBe(92)
    })

    test('4th quarter', () => {
        expect(numberOfDaysInQuarter(new Date('2022-10-15'))).toBe(92)
    })
})
