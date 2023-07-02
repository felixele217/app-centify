import { payoutFrequencyDescription } from '@/utils/Descriptions/payoutFrequencyDescription'
import titleToDescription from '@/utils/Descriptions/titleToDescription'
import { expect, test } from 'vitest'

test('receives the proper response for PayoutFrequencyOptions', function () {
    expect(titleToDescription(['monthly'], 'PayoutFrequencyEnum').length).toBe(1)
    expect(titleToDescription(['monthly'], 'PayoutFrequencyEnum')[0].title).toBe('monthly')
    expect(titleToDescription(['monthly'], 'PayoutFrequencyEnum')[0].description).toBe(
        payoutFrequencyDescription['monthly']
    )
    expect(titleToDescription(['monthly'], 'PayoutFrequencyEnum')[0].current).toBeFalsy()
})
