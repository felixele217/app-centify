import sum from '@/utils/sum'
import { expect, test } from 'vitest'

test('sum function correctly calculates sum', () => {
    expect(sum([0, -1, 1])).toBe(0)
    expect(sum([1, 5, 100])).toBe(106)
    expect(sum([1, -10, -100])).toBe(-109)
})
