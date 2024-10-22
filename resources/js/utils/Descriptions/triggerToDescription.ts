import { TriggerEnum } from '@/types/Enum/TriggerEnum'

export const triggerToDescription: Record<TriggerEnum, string> = {
    'Demo scheduled': "'Demo scheduled' is triggered when the demo_set_by field is set.",
    'Deal won': 'The deal has been closed.',
}
