import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { payoutFrequencyToDescription } from '@/utils/Descriptions/payoutFrequencyToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import { expect, test } from 'vitest'

test('receives the proper response for PayoutFrequencyOptions', function () {
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], payoutFrequencyToDescription).length).toBe(1)
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], payoutFrequencyToDescription)[0].title).toBe('monthly')
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], payoutFrequencyToDescription)[0].description).toBe(
        payoutFrequencyToDescription['monthly']
    )
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], payoutFrequencyToDescription)[0].current).toBeFalsy()
})

test('receives the proper response for TargetVariableOptions', function () {
    expect(enumOptionsToSelectOptionWithDescription(['ARR'], targetVariableToDescription).length).toBe(1)
    expect(enumOptionsToSelectOptionWithDescription(['ARR'], targetVariableToDescription)[0].title).toBe('ARR')
    expect(enumOptionsToSelectOptionWithDescription(['ARR'], targetVariableToDescription)[0].description).toBe(
        targetVariableToDescription['ARR']
    )
    expect(enumOptionsToSelectOptionWithDescription(['ARR'], targetVariableToDescription)[0].current).toBeFalsy()
})
