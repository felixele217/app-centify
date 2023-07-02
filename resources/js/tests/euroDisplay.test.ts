import { expect, test } from 'vitest'
import euroDisplay from '@/utils/euroDisplay'
test('123456 (cents) are returned in 1234,56', () => {
    expect(euroDisplay(123456)).toBe('1.234,56€')
})
