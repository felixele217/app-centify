import Deal from '@/types/Deal'
import sum from '../sum'

export function dealOwnerShare(deal: Deal) {
    return 100 - sum(deal.splits!.map((split) => split.shared_percentage * 100 || 0))
}
