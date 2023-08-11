import { AdditionalPlanFieldEnum } from "@/types/Enum/AdditionalPlanFieldEnum";

export const additionalPlanFieldToDescription: Record<AdditionalPlanFieldEnum, string> = {
    'Kicker': 'Set an additional incentive for your agents achievements.',
    'Cliff': 'Set a minimum  threshold to qualify for a commission.',
    'Cap': 'Cap very high deals to a certain amount.',
}
