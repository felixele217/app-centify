<?php

namespace App\Integrations\Pipedrive;

class PipedriveClientDummy
{
    public function deals()
    {
        $dealsArray = [
            0 => [
                'id' => 1,
                'creator_user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'person_id' => [
                    'active_flag' => true,
                    'name' => 'Hashim Hardy',
                    'email' => [
                        0 => [
                            'label' => 'work',
                            'value' => 'hashim.hardy@lvie.com',
                            'primary' => true,
                        ],
                    ],
                    'phone' => [
                        0 => [
                            'label' => 'work',
                            'value' => '240-707-3884',
                            'primary' => true,
                        ],
                    ],
                    'owner_id' => 17524624,
                    'value' => 2,
                ],
                'org_id' => [
                    'name' => 'ABC Inc',
                    'people_count' => 1,
                    'owner_id' => 17524624,
                    'address' => '9974 Pleasant Point, Ohathlockhouchy, Idaho, 83498-8591, US',
                    'active_flag' => true,
                    'cc_email' => 'paul-sandbox11@pipedrivemail.com',
                    'owner_name' => 'Paul',
                    'value' => 5,
                ],
                'stage_id' => 1,
                'title' => 'ABC Inc deal',
                'value' => 0,
                'currency' => 'USD',
                'add_time' => '2023-06-26 08 =>33 =>25',
                'update_time' => '2023-06-26 08 =>33 =>27',
                'stage_change_time' => null,
                'active' => true,
                'deleted' => false,
                'status' => 'open',
                'probability' => null,
                'next_activity_date' => '2023-06-26',
                'next_activity_time' => null,
                'next_activity_id' => 3,
                'last_activity_id' => null,
                'last_activity_date' => null,
                'lost_reason' => null,
                'visible_to' => '3',
                'close_time' => null,
                'pipeline_id' => 1,
                'won_time' => null,
                'first_won_time' => null,
                'lost_time' => null,
                'products_count' => 0,
                'files_count' => 0,
                'notes_count' => 0,
                'followers_count' => 1,
                'email_messages_count' => 0,
                'activities_count' => 1,
                'done_activities_count' => 0,
                'undone_activities_count' => 1,
                'participants_count' => 1,
                'expected_close_date' => null,
                'last_incoming_mail_time' => null,
                'last_outgoing_mail_time' => null,
                'label' => null,
                'stage_order_nr' => 0,
                'person_name' => 'Hashim Hardy',
                'org_name' => 'ABC Inc',
                'next_activity_subject' => 'Task',
                'next_activity_type' => 'call',
                'next_activity_duration' => null,
                'next_activity_note' => null,
                'formatted_value' => '$0',
                'weighted_value' => 0,
                'formatted_weighted_value' => '$0',
                'weighted_value_currency' => 'USD',
                'rotten_time' => null,
                'owner_name' => 'Paul',
                'cc_email' => 'paul-sandbox11deal1@pipedrivemail.com',
                'org_hidden' => false,
                'person_hidden' => false,
            ],
            1 => [
                'id' => 2,
                'creator_user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'person_id' => [
                    'active_flag' => true,
                    'name' => 'Phyllis Yang',
                    'email' => [
                        0 => [
                            'label' => 'work',
                            'value' => 'phyllis.yang@gmial.com',
                            'primary' => true,
                        ],
                    ],
                    'phone' => [
                        0 => [
                            'label' => 'work',
                            'value' => '313-428-3135',
                            'primary' => true,
                        ],
                    ],
                    'owner_id' => 17524624,
                    'value' => 1,
                ],
                'org_id' => [
                    'name' => 'Silicon Links Inc',
                    'people_count' => 1,
                    'owner_id' => 17524624,
                    'address' => '7938 Dewy Park, Totstalahoeetska, Texas, 79835-6679, US',
                    'active_flag' => true,
                    'cc_email' => 'paul-sandbox11@pipedrivemail.com',
                    'owner_name' => 'Paul',
                    'value' => 2,
                ],
                'stage_id' => 1,
                'title' => 'Silicon Links Inc deal',
                'value' => 0,
                'currency' => 'USD',
                'add_time' => '2023-06-26 08 =>33 =>25',
                'update_time' => '2023-06-26 08 =>33 =>27',
                'stage_change_time' => null,
                'active' => true,
                'deleted' => false,
                'status' => 'open',
                'probability' => null,
                'next_activity_date' => '2023-06-26',
                'next_activity_time' => null,
                'next_activity_id' => 1,
                'last_activity_id' => null,
                'last_activity_date' => null,
                'lost_reason' => null,
                'visible_to' => '3',
                'close_time' => null,
                'pipeline_id' => 1,
                'won_time' => null,
                'first_won_time' => null,
                'lost_time' => null,
                'products_count' => 0,
                'files_count' => 0,
                'notes_count' => 1,
                'followers_count' => 1,
                'email_messages_count' => 0,
                'activities_count' => 1,
                'done_activities_count' => 0,
                'undone_activities_count' => 1,
                'participants_count' => 1,
                'expected_close_date' => null,
                'last_incoming_mail_time' => null,
                'last_outgoing_mail_time' => null,
                'label' => null,
                'stage_order_nr' => 0,
                'person_name' => 'Phyllis Yang',
                'org_name' => 'Silicon Links Inc',
                'next_activity_subject' => 'Meeting',
                'next_activity_type' => 'call',
                'next_activity_duration' => null,
                'next_activity_note' => null,
                'formatted_value' => '$0',
                'weighted_value' => 0,
                'formatted_weighted_value' => '$0',
                'weighted_value_currency' => 'USD',
                'rotten_time' => null,
                'owner_name' => 'Paul',
                'cc_email' => 'paul-sandbox11deal2@pipedrivemail.com',
                'org_hidden' => false,
                'person_hidden' => false,
            ],
            2 => [
                'id' => 3,
                'creator_user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'user_id' => [
                    'id' => 17524624,
                    'name' => 'Paul',
                    'email' => 'paul.sochiera@gmail.com',
                    'has_pic' => 0,
                    'pic_hash' => null,
                    'active_flag' => true,
                    'value' => 17524624,
                ],
                'person_id' => [
                    'active_flag' => true,
                    'name' => 'Cora Santiago',
                    'email' => [
                        0 => [
                            'label' => 'work',
                            'value' => 'cora.santiago@lvie.com',
                            'primary' => true,
                        ],
                    ],
                    'phone' => [
                        0 => [
                            'label' => 'work',
                            'value' => '862-252-9773',
                            'primary' => true,
                        ],
                    ],
                    'owner_id' => 17524624,
                    'value' => 5,
                ],
                'org_id' => [
                    'name' => 'Umbrella Corp',
                    'people_count' => 1,
                    'owner_id' => 17524624,
                    'address' => '207 Rustic Robin Bank, Slicklizzard, New Mexico, 87764-3937, US',
                    'active_flag' => true,
                    'cc_email' => 'paul-sandbox11@pipedrivemail.com',
                    'owner_name' => 'Paul',
                    'value' => 1,
                ],
                'stage_id' => 1,
                'title' => 'Umbrella Corp deal',
                'value' => 0,
                'currency' => 'USD',
                'add_time' => '2023-06-26 08 =>33 =>25',
                'update_time' => '2023-06-26 08 =>33 =>27',
                'stage_change_time' => null,
                'active' => true,
                'deleted' => false,
                'status' => 'open',
                'probability' => null,
                'next_activity_date' => '2023-06-26',
                'next_activity_time' => null,
                'next_activity_id' => 2,
                'last_activity_id' => null,
                'last_activity_date' => null,
                'lost_reason' => null,
                'visible_to' => '3',
                'close_time' => null,
                'pipeline_id' => 1,
                'won_time' => null,
                'first_won_time' => null,
                'lost_time' => null,
                'products_count' => 0,
                'files_count' => 0,
                'notes_count' => 0,
                'followers_count' => 1,
                'email_messages_count' => 0,
                'activities_count' => 1,
                'done_activities_count' => 0,
                'undone_activities_count' => 1,
                'participants_count' => 1,
                'expected_close_date' => null,
                'last_incoming_mail_time' => null,
                'last_outgoing_mail_time' => null,
                'label' => null,
                'stage_order_nr' => 0,
                'person_name' => 'Cora Santiago',
                'org_name' => 'Umbrella Corp',
                'next_activity_subject' => 'Email',
                'next_activity_type' => 'call',
                'next_activity_duration' => null,
                'next_activity_note' => null,
                'formatted_value' => '$0',
                'weighted_value' => 0,
                'formatted_weighted_value' => '$0',
                'weighted_value_currency' => 'USD',
                'rotten_time' => null,
                'owner_name' => 'Paul',
                'cc_email' => 'paul-sandbox11deal3@pipedrivemail.com',
                'org_hidden' => false,
                'person_hidden' => false,
            ],
        ];

        return new CustomResponse($dealsArray);
    }

    public static function dealCount(string $email): int
    {
        $emailCount = 0;

        foreach ((new PipedriveClientDummy())->deals()->getData() as $deal) {
            if ($deal['creator_user_id']['email'] === $email) {
                $emailCount++;
            }
        }

        return $emailCount;
    }
}

class CustomResponse
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function all()
    {
        return $this;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this->getData()), true);
    }

    public function getData()
    {
        return $this->data;
    }
}
