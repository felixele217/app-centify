import roundFloat from '@/utils/roundFloat'
import { expect, test } from 'vitest'

test('roundFloat function correctly rounds floats', () => {
    expect(roundFloat(10.25412, 2)).toBe(10.25)
    expect(roundFloat(10.25412)).toBe(10.25)
    expect(roundFloat(10.25512)).toBe(10.26)
    expect(roundFloat(5.2154, 3)).toBe(5.215)
    expect(roundFloat(5.2156, 3)).toBe(5.216)
})
