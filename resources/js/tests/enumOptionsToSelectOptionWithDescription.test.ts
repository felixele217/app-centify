import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { planCycleToDescription } from '@/utils/Descriptions/planCycleToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import { expect, test } from 'vitest'

test('receives the proper response for PlanCycleOptions', function () {
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], planCycleToDescription).length).toBe(1)
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], planCycleToDescription)[0].title).toBe('monthly')
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], planCycleToDescription)[0].description).toBe(
        planCycleToDescription['monthly']
    )
    expect(enumOptionsToSelectOptionWithDescription(['monthly'], planCycleToDescription)[0].current).toBeFalsy()
})

test('receives the proper response for TargetVariableOptions', function () {
    expect(enumOptionsToSelectOptionWithDescription(['Deal Value'], targetVariableToDescription).length).toBe(1)
    expect(enumOptionsToSelectOptionWithDescription(['Deal Value'], targetVariableToDescription)[0].title).toBe(
        'Deal Value'
    )
    expect(enumOptionsToSelectOptionWithDescription(['Deal Value'], targetVariableToDescription)[0].description).toBe(
        targetVariableToDescription['Deal Value']
    )
    expect(enumOptionsToSelectOptionWithDescription(['Deal Value'], targetVariableToDescription)[0].current).toBeFalsy()
})
