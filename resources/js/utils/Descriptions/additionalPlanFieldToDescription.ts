import { AdditionalPlanFieldEnum } from "@/types/Enum/AdditionalPlanFieldEnum";

export const additionalPlanFieldToDescription: Record<AdditionalPlanFieldEnum, string> = {
    'Kicker': 'A kicker is an additional incentive an agent can receive if a particular target is achieved.\nExample: Close deals with an accumulative ARR of >=150K this quarter to earn 4K on top of your commission.',
    'Cliff': 'A cliff is a certain threshold that needs to be achieved by the agents to qualify for receiving commission.',
    'Cap': 'You can cap the payout for an accepted deal with this function.\nThis is especially helpful for high potential deals that are yet to come through.',
}
