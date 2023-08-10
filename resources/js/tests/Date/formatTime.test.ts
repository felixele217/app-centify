import formatTime from '@/utils/Date/formatTime'
import { expect, test } from 'vitest'

test('formats dates correctly', () => {
    expect(formatTime(new Date('2022-07-06 08:55:22'))).toBe('6 Jul 2022 08:55')
    expect(formatTime(new Date('1999-03-25 18:25:12'))).toBe('25 Mar 1999 18:25')
})

test('undefined or null as inputs return "-"', () => {
    expect(formatTime(undefined)).toBe('-')
    expect(formatTime(null)).toBe('-')
})
