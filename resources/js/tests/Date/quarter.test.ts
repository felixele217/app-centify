import quarter from '@/utils/Date/quarter'
import { expect, test } from 'vitest'

test('returns correct quarters', () => {
    expect(quarter(new Date('2022-01-20'))).toBe(1)
    expect(quarter(new Date('2022-02-20'))).toBe(1)
    expect(quarter(new Date('2022-03-20'))).toBe(1)

    expect(quarter(new Date('2022-04-20'))).toBe(2)
    expect(quarter(new Date('2022-05-20'))).toBe(2)
    expect(quarter(new Date('2022-06-20'))).toBe(2)

    expect(quarter(new Date('2022-07-20'))).toBe(3)
    expect(quarter(new Date('2022-08-20'))).toBe(3)
    expect(quarter(new Date('2022-09-20'))).toBe(3)

    expect(quarter(new Date('2022-10-20'))).toBe(4)
    expect(quarter(new Date('2022-11-20'))).toBe(4)
    expect(quarter(new Date('2022-12-20'))).toBe(4)
})
