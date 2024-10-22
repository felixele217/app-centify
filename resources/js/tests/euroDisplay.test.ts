import euroDisplay from '@/utils/euroDisplay'
import { expect, test } from 'vitest'
test('123456 (cents) are returned in 1234,56', () => {
    expect(euroDisplay(123456)).toBe('1.234,56€')
})

test('123456 (cents) can omit the last fraction digits and return 1234', () => {
    expect(euroDisplay(123456, 0)).toBe('1.235€')
})
