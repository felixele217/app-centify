import Deal from '@/types/Deal'
import { dealOwnerShare } from '@/utils/Deal/dealOwnerShare'
import { expect, test } from 'vitest'

test('calculates deal owner share from existing deal splits', () => {
    const deal = {
        agent: {
            name: 'Deal Owner',
        },
        splits: [
            {
                shared_percentage: 0.1,
            },
            {
                shared_percentage: 0.2,
            },
        ],
    }

    expect(dealOwnerShare(deal as Deal)).toBe(70)
})
