import euroDisplay from '@/utils/euroDisplay'
import formatDate from '@/utils/formatDate'
import { expect, test } from 'vitest'

test('formats dates correctly', () => {
    expect(formatDate(new Date('2022-07-06'))).toBe('6 Jul 2022')
    expect(formatDate(new Date('1999-03-25'))).toBe('25 Mar 1999')
})

test('formats dates in string format correctly', () => {
    expect(formatDate('2022-07-06')).toBe('6 Jul 2022')
    expect(formatDate('1999-03-25')).toBe('25 Mar 1999')
})

test('undefined or null as inputs return "-"', () => {
    expect(formatDate(undefined)).toBe('-')
    expect(formatDate(null)).toBe('-')
})
