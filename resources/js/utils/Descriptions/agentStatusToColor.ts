import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'

export const agentStatusToColor: Record<AgentStatusEnum, string> = {
    active: 'bg-green-100 ring-green-700',
    'on vacation': 'bg-yellow-100 ring-yellow-700',
    sick: 'bg-purple-100 ring-purple-700',
}
