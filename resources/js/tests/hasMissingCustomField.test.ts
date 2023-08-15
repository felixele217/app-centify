import Integration from '@/types/Integration'
import hasMissingCustomField from '@/utils/Integration/hasMissingCustomField'
import { expect, test } from 'vitest'

test('returns false if all fields are set', () => {
    const activeIntegrationWithCustomFields = {
        custom_fields: [
            {
                api_key: '013456789013456789013456789013456789',
            },
        ],
    }

    expect(hasMissingCustomField(activeIntegrationWithCustomFields as Integration)).toBe(false)
})

test('returns true if not all fields are set', () => {
    const activeIntegrationWithoutCustomFields = {
        custom_fields: [],
    }

    // @ts-ignore
    expect(hasMissingCustomField(activeIntegrationWithoutCustomFields)).toBe(true)
})
