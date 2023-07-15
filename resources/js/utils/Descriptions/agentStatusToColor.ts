import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'

export const agentStatusToColor: Record<AgentStatusEnum, string> = {
    active: 'bg-indigo-50 ring-indigo-600',
    'on vacation': 'bg-yellow-50 ring-yellow-600',
    sick: 'bg-green-50 ring-green-600',
}
