import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'

export const payoutFrequencyToDescription: Record<PayoutFrequencyEnum, string> = {
    monthly: 'Your agents will be compensated monthly.',
    quarterly: 'Your agents will be compensated quarterly.',
}
