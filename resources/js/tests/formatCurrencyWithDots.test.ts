import formatCurrencyWithDots from '@/utils/formatCurrencyWithDots'
import { expect, it } from 'vitest'

it('returns correct format', () => {
    expect(formatCurrencyWithDots('123')).toBe('123')
    expect(formatCurrencyWithDots('123456')).toBe('123.456')
    expect(formatCurrencyWithDots('1234567')).toBe('1.234.567')
})
